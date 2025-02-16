<?php

namespace RodrigoPedra\RecordProcessor;

use IteratorIterator;
use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use Traversable;

class Source extends IteratorIterator
{
    protected Reader $reader;

    protected RecordParser $recordParser;

    public function __construct(Reader $reader, RecordParser $recordParser)
    {
        parent::__construct($reader);

        $this->reader = $reader;
        $this->recordParser = $recordParser;
    }

    public function current(): mixed
    {
        $result = $this->recordParser->parseRecord($this->reader, parent::current());

        if ($result instanceof Traversable) {
            return $result;
        }

        return is_array($result) ? $result : [$result];
    }

    public function open(): void
    {
        $this->reader->open();
    }

    public function close(): void
    {
        $this->reader->close();
    }

    public function getLineCount(): int
    {
        return $this->reader->getLineCount();
    }
}
