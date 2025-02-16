<?php

namespace RodrigoPedra\RecordProcessor\Traits\Readers;

use Iterator;

trait HasInnerIterator
{
    protected ?Iterator $iterator = null;

    public function current(): mixed
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): int
    {
        return $this->lineCount;
    }

    public function valid(): bool
    {
        $valid = ! is_null($this->iterator) && $this->iterator->valid();

        if ($valid) {
            $this->incrementLineCount();
        }

        return $valid;
    }

    public function rewind(): void
    {
        $this->lineCount = 0;

        $this->iterator->rewind();
    }

    public function getInnerIterator(): ?Iterator
    {
        return $this->iterator;
    }

    protected function setInnerIterator(?Iterator $iterator = null): void
    {
        $this->iterator = $iterator;
    }
}
