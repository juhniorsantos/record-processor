<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\HasPrefix;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;

class EchoWriter extends FileWriter implements ConfigurableWriter
{
    use HasPrefix, NoOutput;

    public function __construct()
    {
        parent::__construct('php://output');
    }

    public function open(): void
    {
        $this->lineCount = 0;
    }

    public function append(mixed $content): void
    {
        $prefix = $this->getPrefix();

        if ($prefix) {
            $this->file->fwrite($prefix.': ');
        }

        if (! is_string($content)) {
            $content = var_export($content, true);
        }

        $this->file->fwrite($content);
        $this->file->fwrite(PHP_EOL);
        $this->file->fwrite(PHP_EOL);

        $this->incrementLineCount();
    }

    public function getConfigurableMethods(): array
    {
        return ['setPrefix'];
    }

    public function createConfigurator(): Configurator
    {
        return new WriterConfigurator($this, true, true);
    }
}
