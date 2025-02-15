<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

class FileConfig
{
    public const string INPUT_STREAM = 'php://input';
    public const string OUTPUT_STREAM = 'php://output';
    public const string TEMP_FILE = 'php://temp';
    public const int TEMP_FILE_MEMORY_SIZE = 4194304; // 4MB
}