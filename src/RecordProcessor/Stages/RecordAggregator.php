<?php

namespace RodrigoPedra\RecordProcessor\Stages;

use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageFlusher;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStageHandler;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregateFactory;
use RodrigoPedra\RecordProcessor\Records\RecordKeyAggregate;
use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;

class RecordAggregator implements ProcessorStageFlusher, ProcessorStageHandler, RecordAggregateFactory
{
    protected ?RecordAggregate $aggregateRecord = null;

    protected RecordAggregateFactory|RecordAggregator $recordAggregateFactory;

    public function __construct(?RecordAggregateFactory $recordAggregateFactory = null)
    {
        $this->recordAggregateFactory = $recordAggregateFactory ?: $this;
    }

    public function handle(Record $record): ?Record
    {
        if (is_null($this->aggregateRecord)) {
            $this->setAggregateRecord($record); // first record

            return null;
        }

        if ($this->aggregateRecord->pushRecord($record)) {
            return null;
        }

        return $this->setAggregateRecord($record);
    }

    public function flush(FlushPayload $payload): mixed
    {
        $payload->setRecord($this->aggregateRecord);

        return $payload;
    }

    protected function setAggregateRecord(Record $record): ?Record
    {
        if (! $record->valid()) {
            return null;
        }

        $current = $this->aggregateRecord;

        $this->aggregateRecord = $this->recordAggregateFactory->makeRecordAggregate($record);

        return $current;
    }

    public function makeRecordAggregate(Record $record): RecordKeyAggregate
    {
        // default RecordAggregate
        return new RecordKeyAggregate($record);
    }
}
