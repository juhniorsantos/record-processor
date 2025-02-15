<?php

namespace RodrigoPedra\RecordProcessor\Helpers\FileValidators;

class TextFileValidator implements FileTypeValidator
{
    public function isValid(string $extension): bool
    {
        return strtolower($extension) === 'txt';
    }

    public function getMimeType(string $extension): string
    {
        return 'text/plain';
    }
}