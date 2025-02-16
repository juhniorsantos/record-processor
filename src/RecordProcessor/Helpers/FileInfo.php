<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use InvalidArgumentException;
use SplFileInfo;
use SplFileObject;
use SplTempFileObject;

use function RodrigoPedra\RecordProcessor\value_or_null;

final class FileInfo extends SplFileInfo
{
    const string INPUT_STREAM = 'php://input';

    const string OUTPUT_STREAM = 'php://output';

    const string TEMP_FILE = 'php://temp';

    const int TEMP_FILE_MEMORY_SIZE = 4194304; // 4MB

    public function getExtension(): string
    {
        return strtolower(parent::getExtension());
    }

    public function getFileInfo($class = null): SplFileInfo
    {
        $class = value_or_null($class);
        $class = $class ?: self::class;

        return parent::getFileInfo($class);
    }

    public function isTempFile(): bool
    {
        if ($this instanceof SplTempFileObject) {
            return true;
        }

        return substr($this->getPathname(), 0, 10) === self::TEMP_FILE;
    }

    public function guessMimeType(): false|string
    {
        $mimeMap = [
            'csv' => 'text/csv',
            'txt' => 'text/plain',
        ];

        $extension = $this->getExtension();

        if (isset($mimeMap[$extension])) {
            return $mimeMap[$extension];
        }

        return mime_content_type($this->getBasename());
    }

    public function isCSV(): bool
    {
        return $this->getExtension() === 'csv';
    }

    public function getBasenameWithoutExtension(): string
    {
        $extension = $this->getExtension();
        $extension = $extension ? '.'.$extension : $extension;

        return $this->getBasename($extension);
    }

    public static function createTempFileObject(): SplTempFileObject
    {
        return new SplTempFileObject(self::TEMP_FILE_MEMORY_SIZE);
    }

    public static function createFileObject($file, $mode): SplTempFileObject|SplFileObject
    {
        if ($file === self::TEMP_FILE) {
            return self::createTempFileObject();
        }

        if (is_string($file)) {
            $fileInfo = new self($file);

            return $fileInfo->isTempFile() ? self::createTempFileObject() : $fileInfo->openFile($mode);
        }

        if (! $file instanceof SplFileObject) {
            throw new InvalidArgumentException('File should be a path to a file or a SplFileObject');
        }

        $fileInfo = $file->getFileInfo(self::class);

        if ($fileInfo->isTempFile()) {
            return $file;
        }

        return $fileInfo->openFile($mode);
    }

    public static function createWritableFileObject($file, $mode = 'wb'): SplTempFileObject|SplFileObject
    {
        $file = self::createFileObject($file, $mode);

        /** @var FileInfo $fileInfo */
        $fileInfo = $file->getFileInfo(self::class);

        if ($fileInfo->isTempFile()) {
            $file->ftruncate(0);

            return $file;
        }

        if ($fileInfo->getPathname() === self::OUTPUT_STREAM) {
            return $file;
        }

        if (! $fileInfo->isWritable()) {
            $fileName = $fileInfo->getPathname();

            throw new InvalidArgumentException("File {$fileName} is not writable");
        }

        return $file;
    }

    public static function createReadableFileObject($file, $mode = 'rb'): SplTempFileObject|SplFileObject
    {
        $file = self::createFileObject($file, $mode);

        $fileInfo = $file->getFileInfo(self::class);

        if (! $fileInfo->isTempFile() && ! $fileInfo->isReadable()) {
            $fileName = $fileInfo->getPathname();

            throw new InvalidArgumentException("File {$fileName} is not readable");
        }

        $file->rewind();

        return $file;
    }
}
