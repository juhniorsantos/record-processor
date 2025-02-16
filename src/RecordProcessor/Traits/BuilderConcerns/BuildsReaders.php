<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use Illuminate\Support\Collection;
use Iterator;
use PDO;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableReader;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Readers\ArrayReader;
use RodrigoPedra\RecordProcessor\Readers\CollectionReader;
use RodrigoPedra\RecordProcessor\Readers\CSVFileReader;
use RodrigoPedra\RecordProcessor\Readers\IteratorReader;
use RodrigoPedra\RecordProcessor\Readers\PDOReader;
use RodrigoPedra\RecordProcessor\Readers\TextFileReader;
use RodrigoPedra\RecordProcessor\Records\Parsers\ArrayRecordParser;

trait BuildsReaders
{
    protected Reader $reader;

    public function readFromArray(array $items): static
    {
        $this->reader = new ArrayReader($items);

        if (is_null($this->recordParser)) {
            $this->usingParser(new ArrayRecordParser);
        }

        return $this;
    }

    public function readFromCollection(Collection $collection): static
    {
        $this->reader = new CollectionReader($collection);

        if (is_null($this->recordParser)) {
            $this->usingParser(new ArrayRecordParser);
        }

        return $this;
    }

    public function readFromCSVFile($fileName, ?callable $configurator = null): static
    {
        $this->reader = new CSVFileReader($fileName);

        if (is_null($this->recordParser)) {
            $this->usingParser(new ArrayRecordParser);
        }

        $this->configureReader($this->reader, $configurator);

        return $this;
    }

    public function readFromIterator(Iterator $iterator): static
    {
        $this->reader = new IteratorReader($iterator);

        return $this;
    }

    public function readFromPDO(PDO $pdo, $query, array $parameters = []): static
    {
        $this->reader = new PDOReader($pdo, $query);
        $this->reader->setQueryParameters($parameters);

        if (is_null($this->recordParser)) {
            $this->usingParser(new ArrayRecordParser);
        }

        return $this;
    }

    public function readFromTextFile($fileName): static
    {
        $this->reader = new TextFileReader($fileName);

        return $this;
    }

    protected function configureReader(ConfigurableReader $reader, ?callable $configurator = null): ?\RodrigoPedra\RecordProcessor\Helpers\Configurator
    {
        if (is_null($configurator)) {
            return null;
        }

        $readerConfigurator = $reader->createConfigurator();

        call_user_func_array($configurator, [$readerConfigurator]);

        return $readerConfigurator;
    }
}
