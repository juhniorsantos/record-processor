<?php

namespace RodrigoPedra\RecordProcessor;

use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use RodrigoPedra\RecordProcessor\Contracts\ProcessorStage;
use RodrigoPedra\RecordProcessor\Stages\DeferredStageBuilder;
use RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

class ProcessorBuilder implements LoggerAwareInterface
{
    use BuilderConcerns\BuildsCompilers,
        BuilderConcerns\BuildsFormatter,
        BuilderConcerns\BuildsReaders,
        BuilderConcerns\BuildsSource,
        BuilderConcerns\BuildsStages,
        BuilderConcerns\BuildsWriters;

    protected ?LoggerInterface $logger = null;

    /** @var ProcessorStage[] */
    protected array $stages = [];

    public function build(): Processor
    {
        $source = $this->makeSource();

        $converter = new Processor($source);

        foreach ($this->stages as $stage) {
            if ($stage instanceof DeferredStageBuilder) {
                // deferred stage creation
                $stage = $stage->build();
            }

            $converter->addStage($stage);
        }

        return $converter;
    }

    public function addStage(ProcessorStage $stage): static
    {
        $this->stages[] = $stage;

        return $this;
    }

    public function setLogger(?LoggerInterface $logger = null): void
    {
        if (is_null($logger)) {
            return;
        }

        $this->logger = $logger;
    }

    protected function getLogger(): LoggerInterface
    {
        if (is_null($this->logger)) {
            throw new InvalidArgumentException('Missing Logger instance. Use setLogger(...) to provide an instance');
        }

        return $this->logger;
    }
}
