<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Helpers\FileConfig;
use SplFileObject;
use RodrigoPedra\RecordProcessor\Helpers\FileInfo;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Traits\CountsLines;

abstract class FileWriter implements Writer
{
    use CountsLines;

    protected SplFileObject|null|\SplTempFileObject $file = null;

    protected null|\SplFileInfo|FileInfo $fileInfo = null;

    public function __construct($file = null)
    {
        $file = is_null($file) ? FileConfig::TEMP_FILE : $file;

        $this->file = FileInfo::createWritableFileObject($file, 'wb');
        $this->fileInfo = $this->file->getFileInfo(FileInfo::class);
    }

    public function open(): void
    {
        $this->lineCount = 0;
        $this->file->ftruncate(0);
    }

    public function close(): void
    {
        //
    }

    /**
     * @return mixed
     */
    public function output(): mixed
    {
        return FileInfo::createReadableFileObject($this->file, 'rb');
    }
}
