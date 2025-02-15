<?php

namespace RodrigoPedra\RecordProcessor;

use RuntimeException;
use League\Pipeline\PipelineBuilder;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Traits\CountsRecords;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Helpers\StopOnNullPipelineProcessor;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\ProcessorOutput;
use RodrigoPedra\RecordProcessor\Contracts\Processor as ProcessorContract;

class Processor implements ProcessorContract
{
    use CountsRecords;

    protected Source $source;
    protected PipelineBuilder $stages;
    protected PipelineBuilder $flushers;

    public function __construct(Source $source)
    {
        $this->source = $source;
        $this->stages = new PipelineBuilder;
        $this->flushers = new PipelineBuilder;
    }

    public function process(): ProcessorOutput
    {
        $this->recordCount = 0;

        try {
            $this->source->open();

            /** @var \League\Pipeline\Pipeline $stages */
            $stages = $this->stages->build(new StopOnNullPipelineProcessor);

            $this->recordCount = 0;

            foreach ($this->source as $records) {
                foreach ($records as $record) {
                    if (! $record instanceof Record) {
                        throw new RuntimeException('Record parser should return or generate a Record instance');
                    }

                    if ($record->valid()) {
                        $this->incrementRecordCount();
                    }

                    $stages->process($record);
                }
            }

            $flushers = $this->flushers->build();

            $payload = $flushers->process(new FlushPayload);

            $results = new ProcessorOutput(
                $this->source->getLineCount(),
                $this->getRecordCount(),
                $payload->getLineCount(),
                $payload->getRecordCount(),
                $payload->getOutput()
            );

            return $results;
        } finally {
            $this->source->close();
        }
    }

    public function addStage(ProcessorStage $stage): void
    {
        if ($stage instanceof ProcessorStageHandler) {
            $this->addProcessorStageHandler($stage);
        }

        if ($stage instanceof ProcessorStageFlusher) {
            $this->addProcessorStageFlusher($stage);
        }
    }

    protected function addProcessorStageHandler(ProcessorStageHandler $stage): void
    {
        $this->stages->add(function (Record $record = null) use ($stage) {
            return $stage->handle($record);
        });
    }

    protected function addProcessorStageFlusher(ProcessorStageFlusher $stage): void
    {
        $this->flushers->add(function (FlushPayload $payload) use ($stage) {
            return $stage->flush($payload);
        });
    }
}
