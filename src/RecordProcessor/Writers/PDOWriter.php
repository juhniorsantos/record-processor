<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use Exception;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use RodrigoPedra\RecordProcessor\Contracts\ConfigurableWriter;
use RodrigoPedra\RecordProcessor\Helpers\Configurator;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\NoOutput;
use RuntimeException;

use function RodrigoPedra\RecordProcessor\is_associative_array;
use function RodrigoPedra\RecordProcessor\value_or_null;

class PDOWriter implements ConfigurableWriter
{
    use CountsLines, NoOutput;

    protected ?PDO $pdo = null;

    protected ?PDOStatement $writer = null;

    protected mixed $tableName;

    protected int $columnCount;

    protected array $columns = [];

    protected string $valuesStatement;

    protected bool $usesTransaction = true;

    protected bool $inTransaction = false;

    protected ?bool $isAssociative = null;

    public function __construct(PDO $pdo, $tableName, array $columns)
    {
        $this->columnCount = count($columns);

        if ($this->columnCount < 1) {
            throw new InvalidArgumentException('Columns array should contain at least one column');
        }

        $this->pdo = $pdo;
        $this->tableName = value_or_null($tableName);
        $this->columns = is_associative_array($columns) ? array_keys($columns) : $columns;
        $this->valuesStatement = $this->formatValuesString($this->columnCount);
    }

    public function setUsesTransaction(bool $usesTransaction): void
    {
        $this->usesTransaction = $usesTransaction;
    }

    public function open(): void
    {
        $this->lineCount = 0;

        if ($this->usesTransaction === true) {
            $this->pdo->beginTransaction();
            $this->inTransaction = true;
        }
    }

    public function close(): void
    {
        if ($this->inTransaction) {
            $this->pdo->commit();
            $this->inTransaction = false;
        }

        $this->writer = null;
    }

    /**
     * @throws Exception
     */
    public function append(mixed $content): void
    {
        if (! is_array($content)) {
            throw new RuntimeException('content for PDOWriter should be an array');
        }

        try {
            $data = $this->prepareValuesForInsert($content);
            $writer = $this->prepareWriter(1);

            if (! $writer->execute($data)) {
                throw new RuntimeException('Could not write PDO records');
            }

            $this->incrementLineCount($this->writer->rowCount());
        } catch (Exception $exception) {
            if ($this->inTransaction) {
                $this->pdo->rollBack();
                $this->inTransaction = false;
            }

            throw $exception;
        }
    }

    protected function prepareWriter($count): false|PDOStatement|null
    {
        if (! is_null($this->writer)) {
            return $this->writer;
        }

        $query = $this->formatQueryStatement($count);

        $this->writer = $this->pdo->prepare($query);

        return $this->writer;
    }

    protected function formatQueryStatement($count): string
    {
        $tokens = [
            'INSERT INTO',
            $this->tableName,
            $this->sanitizeColumns($this->columns),
            'VALUES',
            implode(',', array_fill(0, $count, $this->valuesStatement)),
        ];

        return implode(' ', $tokens);
    }

    protected function formatValuesString($valuesQuantity): string
    {
        return '('.implode(',', array_fill(0, $valuesQuantity, '?')).')';
    }

    protected function sanitizeColumns(array $columns): string
    {
        $columns = value_or_null($columns);
        $columns = array_map(function ($column) {
            return value_or_null($column);
        }, $columns);

        return '('.implode(',', $columns).')';
    }

    protected function prepareValuesForInsert(array $values): array
    {
        if (count($values) !== $this->columnCount) {
            throw new RuntimeException('Record column count does not match PDOWriter column definition');
        }

        if (is_null($this->isAssociative)) {
            $this->isAssociative = is_associative_array($values);

            if ($this->isAssociative) {
                sort($this->columns);
            }
        }

        if ($this->isAssociative) {
            ksort($values);

            return array_values($values);
        }

        return $values;
    }

    public function getConfigurableMethods(): array
    {
        return ['setUsesTransaction'];
    }

    public function createConfigurator(): Configurator
    {
        return new WriterConfigurator($this, false, false);
    }
}
