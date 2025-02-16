<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Writers\TextFileWriter;

class ExampleRecordFormatter implements RecordFormatter
{
    /**
     * @param  ExampleRecord|Record  $record
     */
    public function formatRecord(Writer $writer, Record $record): bool
    {
        if (! $record->valid()) {
            return false;
        }

        $content = $writer instanceof TextFileWriter
            ? $record->toText()
            : $record->toArray();

        $writer->append($content);

        return true;
    }
}
