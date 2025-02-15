<?php

namespace RodrigoPedra\RecordProcessor\Helpers\FileValidators;

class CSVFileValidator implements FileTypeValidator
{
    public function isValid(string $extension): bool
    {
        return strtolower($extension) === 'csv';
    }

    public function getMimeType(string $extension): string
    {
        return 'text/csv';
    }
}