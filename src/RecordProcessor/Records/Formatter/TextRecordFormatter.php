<?php

namespace RodrigoPedra\RecordProcessor\Records\Formatter;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RuntimeException;

class TextRecordFormatter implements RecordFormatter
{
    protected mixed $writesValidRecords = true;

    public function __construct($writesValidRecords = true)
    {
        $this->writesValidRecords = $writesValidRecords;
    }

    public function formatRecord(Writer $writer, Record $record): bool
    {
        if (! $record instanceof TextRecord) {
            $className = get_class($record);

            throw new RuntimeException("'{$className}' should implement TextRecord interface");
        }

        if ($this->writesValidRecords xor $record->valid()) {
            return false;
        }

        $writer->append($record->toText() ?: '');

        return true;
    }
}
