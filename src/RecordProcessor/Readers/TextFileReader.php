<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\NewLines;

class TextFileReader extends FileReader
{
    public function open(): void
    {
        parent::open();

        $this->setInnerIterator($this->file);
    }

    public function current(): mixed
    {
        $content = $this->iteratorCurrent();

        return rtrim($content, NewLines::WINDOWS_NEWLINE); // removes line endings
    }

    public function valid(): bool
    {
        return $this->iteratorValid() && ! $this->iterator->eof();
    }
}
