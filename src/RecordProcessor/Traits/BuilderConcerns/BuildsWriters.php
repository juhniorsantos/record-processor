<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use PDO;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Records\Formatter\ArrayRecordFormatter;
use RodrigoPedra\RecordProcessor\Records\Formatter\TextRecordFormatter;
use RodrigoPedra\RecordProcessor\Writers\CSVFileWriter;
use RodrigoPedra\RecordProcessor\Writers\EchoWriter;
use RodrigoPedra\RecordProcessor\Writers\LogWriter;
use RodrigoPedra\RecordProcessor\Writers\PDOBufferedWriter;
use RodrigoPedra\RecordProcessor\Writers\PDOWriter;
use RodrigoPedra\RecordProcessor\Writers\TextFileWriter;

trait BuildsWriters
{
    public function writeToCSVFile($fileName = null, ?callable $configurator = null): static
    {
        if (is_callable($fileName)) {
            $configurator = $fileName;
            $fileName = null;
        }

        $writer = new CSVFileWriter($fileName);

        if (is_null($this->recordFormatter)) {
            $this->usingFormatter(new ArrayRecordFormatter);
        }

        $this->addCompiler($writer, $this->configureWriter($writer, $configurator));

        return $this;
    }

    public function writeToEcho(?callable $configurator = null): static
    {
        $writer = new EchoWriter;

        if (is_null($this->recordFormatter)) {
            $this->usingFormatter(new TextRecordFormatter);
        }

        $this->addCompiler($writer, $this->configureWriter($writer, $configurator));

        return $this;
    }

    public function writeToLog(?callable $configurator = null): static
    {
        $writer = new LogWriter($this->getLogger());

        if (is_null($this->recordFormatter)) {
            $this->usingFormatter(new ArrayRecordFormatter);
        }

        $this->addCompiler($writer, $this->configureWriter($writer, $configurator));

        return $this;
    }

    public function writeToPDO(
        PDO $pdo,
        $tableName,
        array $columns,
        $buffered = true,
        ?callable $configurator = null
    ): static {
        $writer = $buffered === true
            ? new PDOBufferedWriter($pdo, $tableName, $columns)
            : new PDOWriter($pdo, $tableName, $columns);

        if (is_null($this->recordFormatter)) {
            $this->usingFormatter(new ArrayRecordFormatter);
        }

        $this->addCompiler($writer, $this->configureWriter($writer, $configurator));

        return $this;
    }

    public function writeToTextFile($fileName = null, ?callable $configurator = null): static
    {
        if (is_callable($fileName)) {
            $configurator = $fileName;
            $fileName = null;
        }

        $writer = new TextFileWriter($fileName);

        if (is_null($this->recordFormatter)) {
            $this->usingFormatter(new TextRecordFormatter);
        }

        $this->addCompiler($writer, $this->configureWriter($writer, $configurator));

        return $this;
    }

    protected function configureWriter(ConfigurableWriter $writer, ?callable $configurator = null): ?Configurator
    {
        if (is_null($configurator)) {
            return null;
        }

        $writerConfigurator = $writer->createConfigurator();

        call_user_func_array($configurator, [$writerConfigurator]);

        return $writerConfigurator;
    }
}
