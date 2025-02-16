<?php

namespace RodrigoPedra\RecordProcessor\Records\Formatter;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;

class ArrayRecordFormatter implements RecordFormatter
{
    protected mixed $writesValidRecords = true;

    public function __construct($writesValidRecords = true)
    {
        $this->writesValidRecords = $writesValidRecords;
    }

    public function formatRecord(Writer $writer, Record $record): bool
    {
        if ($this->writesValidRecords xor $record->valid()) {
            return false;
        }

        $writer->append($record->toArray());

        return true;
    }
}
