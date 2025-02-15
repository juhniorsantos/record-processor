<?php

namespace RodrigoPedra\RecordProcessor\Helpers\FileValidators;

class FileTypeValidator
{
    private array $mimeMap = [
        'csv' => 'text/csv',
        'txt' => 'text/plain',
    ];

    public function getMimeType(string $extension): string|false
    {
        $extension = strtolower($extension);
        return $this->mimeMap[$extension] ?? mime_content_type($extension);
    }
}