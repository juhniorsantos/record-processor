<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use Illuminate\Support\Arr;
use League\Csv\ByteSequence;
use League\Csv\Writer as RawCsvWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Traits\CsvControls;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;

class CSVFileWriter extends FileWriter implements ConfigurableWriter, ByteSequence, NewLines
{
    use CsvControls;

    const DATA_TRAILLER = '<< <<';

    /** @var RawCsvWriter|null */
    protected $writer = null;

    public function __construct($file = null)
    {
        parent::__construct($file);

        // defaults
        $this->setDelimiter(';');
        $this->setNewline(static::WINDOWS_NEWLINE);
        $this->setOutputBOM(static::BOM_UTF8);
    }

    public function open(): void
    {
        parent::open();

        $this->writer = RawCsvWriter::createFromFileObject($this->file);

        $this->writer->setOutputBOM($this->getOutputBOM());
        $this->writer->setDelimiter($this->getDelimiter());
        $this->writer->setEnclosure($this->getEnclosure());
        $this->writer->setNewline($this->getNewline());
        $this->writer->setEscape($this->getEscape());
    }

    public function close(): void
    {
        $this->writer = null;
    }

    public function append(mixed $content): void
    {
        $this->writer->insertOne(Arr::wrap($content));

        $this->incrementLineCount();
    }

    public function getConfigurableMethods(): array
    {
        return [
            'setOutputBOM',
            'setDelimiter',
            'setEnclosure',
            'setEscape',
            'setNewline',
        ];
    }

    public function createConfigurator(): \RodrigoPedra\RecordProcessor\Helpers\Configurator
    {
        return new WriterConfigurator($this, true, true);
    }
}
