<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\HasLogger;
use RodrigoPedra\RecordProcessor\Traits\HasPrefix;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;

class LogWriter implements ConfigurableWriter, LoggerAwareInterface
{
    use CountsLines, HasLogger, HasPrefix, NoOutput;

    /** @var string */
    protected $level;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);

        $this->setLevel(LogLevel::INFO);
    }

    public function setLevel($level): void
    {
        if (! in_array($level, [
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::ERROR,
            LogLevel::WARNING,
            LogLevel::NOTICE,
            LogLevel::INFO,
            LogLevel::DEBUG,
        ], true)) {
            throw new InvalidArgumentException('Invalid log level. See Psr\\Log\\LogLevel class for available levels');
        }

        $this->level = $level;
    }

    public function open(): void
    {
        $this->lineCount = 0;
    }

    public function close(): void
    {
        //
    }

    public function append(mixed $content): void
    {
        $this->logger->log($this->level, $this->getPrefix(), Arr::wrap($content));

        $this->incrementLineCount();
    }

    public function getConfigurableMethods(): array
    {
        return [
            'setLevel',
            'setPrefix',
        ];
    }

    public function createConfigurator(): Configurator
    {
        return new WriterConfigurator($this, true, true);
    }
}
