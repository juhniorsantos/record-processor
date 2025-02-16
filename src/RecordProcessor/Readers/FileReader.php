<?php

namespace RodrigoPedra\RecordProcessor\Readers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;
use RodrigoPedra\RecordProcessor\Traits\Readers\HasInnerIterator;
use SplFileObject;

abstract class FileReader implements Reader
{
    use CountsLines, HasInnerIterator {
        current as iteratorCurrent;
        valid as iteratorValid;
    }

    protected ?SplFileObject $file = null;

    protected null|\SplFileInfo|FileInfo $fileInfo = null;

    public function __construct($file)
    {
        $this->file = FileInfo::createReadableFileObject($file, 'rb');
        $this->fileInfo = $this->file->getFileInfo(FileInfo::class);
    }

    public function open(): void
    {
        $this->lineCount = 0;
    }

    public function close(): void
    {
        $this->setInnerIterator(null);
    }
}
