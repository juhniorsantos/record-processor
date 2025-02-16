<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use Iterator;
use PDO;
use PDOStatement;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;

class PDOReader implements Reader
{
    use CountsLines;

    protected ?PDO $pdo = null;

    protected ?PDOStatement $reader = null;

    protected string $query;

    protected ?array $queryParameters = [];

    protected array|bool|null $currentRecord = false;

    public function __construct(PDO $pdo, $query)
    {
        $this->pdo = $pdo;
        $this->query = $query;

        if ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            $this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        }
    }

    public function setQueryParameters(array $queryParameters): void
    {
        $this->queryParameters = $queryParameters;
    }

    public function open(): void
    {
        $this->lineCount = 0;

        if (is_null($this->reader)) {
            $this->reader = $this->pdo->prepare($this->query, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY]);
            $this->reader->setFetchMode(PDO::FETCH_ASSOC);
        } else {
            $this->reader->closeCursor();
        }

        $this->currentRecord = null;
    }

    public function close(): void
    {
        $this->reader = null;
        $this->pdo = null;
        $this->queryParameters = null;
        $this->currentRecord = false;
    }

    public function current(): mixed
    {
        return $this->currentRecord;
    }

    public function next(): void
    {
        $this->currentRecord = $this->reader->fetch() ?: null;
    }

    public function key(): mixed
    {
        return $this->lineCount;
    }

    public function valid(): bool
    {
        $valid = ! is_null($this->currentRecord);

        if ($valid) {
            $this->incrementLineCount();
        }

        return $valid;
    }

    public function rewind(): void
    {
        if (! is_null($this->currentRecord)) {
            $this->reader->closeCursor();
            $this->currentRecord = null;
        }

        if ($this->reader->execute($this->queryParameters) === false) {
            return;
        }

        $this->currentRecord = $this->reader->fetch() ?: null;
    }

    public function getInnerIterator(): Iterator|static
    {
        return $this;
    }
}
