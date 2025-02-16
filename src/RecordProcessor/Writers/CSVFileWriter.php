<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use Illuminate\Support\Arr;
use League\Csv\ByteSequence;
use League\Csv\Writer as RawCsvWriter;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CsvControls;

class CSVFileWriter extends FileWriter implements ByteSequence, ConfigurableWriter, NewLines
{
    use CsvControls;

    const string DATA_TRAILLER = '<< <<';

    protected ?RawCsvWriter $writer = null;

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

    public function createConfigurator(): Configurator
    {
        return new WriterConfigurator($this, true, true);
    }
}
