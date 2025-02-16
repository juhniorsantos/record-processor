<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;
use RuntimeException;

class TextFileWriter extends FileWriter implements ConfigurableWriter, NewLines
{
    protected string $newLine = self::WINDOWS_NEWLINE;

    public function getNewLine(): string
    {
        return $this->newLine;
    }

    /**
     * @param  string  $newLine
     */
    public function setNewLine($newLine): void
    {
        $this->newLine = $newLine;
    }

    public function append(mixed $content): void
    {
        if (! is_string($content)) {
            throw new RuntimeException('content for TextWriter should be a string');
        }

        $content = sprintf('%s%s', $content, $this->getNewLine());
        $this->file->fwrite($content);

        $this->incrementLineCount(substr_count($content, $this->getNewLine()));
    }

    public function getConfigurableMethods(): array
    {
        return ['setNewLine'];
    }

    public function createConfigurator(): Configurator
    {
        return new WriterConfigurator($this, true, true);
    }
}
