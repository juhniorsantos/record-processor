<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader as RawCsvReader;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Traits\CsvControls;

class CSVFileReader extends FileReader implements ConfigurableReader
{
    use CsvControls;

    protected bool $useFirstRowAsHeader = true;

    public function __construct($file)
    {
        parent::__construct($file);

        // default values
        $this->setDelimiter(';');
        $this->setEnclosure('"');
        $this->setEscape('\\');
        $this->useFirstRowAsHeader(true);
    }

    public function useFirstRowAsHeader($firstRowAsHeader = true): void
    {
        $this->useFirstRowAsHeader = $firstRowAsHeader;
    }

    /**
     * @throws InvalidArgument
     * @throws Exception
     */
    public function open(): void
    {
        parent::open();

        /** @var RawCsvReader $csvReader */
        $csvReader = RawCsvReader::createFromFileObject($this->file);

        $csvReader->setDelimiter($this->getDelimiter());
        $csvReader->setEnclosure($this->getEnclosure());
        $csvReader->setEscape($this->getEscape());

        if ($this->useFirstRowAsHeader) {
            $csvReader->setHeaderOffset(0);
        }

        $this->setInnerIterator($csvReader->getRecords());
    }

    public function getConfigurableMethods(): array
    {
        return [
            'setDelimiter',
            'setEnclosure',
            'setEscape',
            'useFirstRowAsHeader',
        ];
    }

    public function createConfigurator(): Configurator
    {
        return new Configurator($this);
    }
}
