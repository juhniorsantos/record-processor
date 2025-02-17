<?php

namespace RodrigoPedra\RecordProcessor\Examples\RecordObjects;

use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;
use RodrigoPedra\RecordProcessor\Contracts\RecordFormatter;
use RodrigoPedra\RecordProcessor\Contracts\Writer;
use RodrigoPedra\RecordProcessor\Records\SimpleRecord;
use RuntimeException;

class ExampleRecordAggregateFormatter implements RecordFormatter
{
    protected ExampleRecordFormatter $recordFormatter;

    public function __construct()
    {
        $this->recordFormatter = new ExampleRecordFormatter;
    }

    public function formatRecord(Writer $writer, Record $record): bool
    {
        if (! $record instanceof RecordAggregate) {
            throw new RuntimeException('Record for ExampleRecordAggregateFormatter should implement RecordAggregate interface');
        }

        if (! $record->valid()) {
            return false;
        }

        $children = $this->formatChildren($record->getRecords());
        $content = [
            'name' => $record->getKey(),
            'email' => $children,
        ];

        return $this->recordFormatter->formatRecord($writer, new SimpleRecord($content));
    }

    public function formatChildren(array $children): string
    {
        return implode(', ', array_map(static function (Record $record) {
            return $record->get('email');
        }, $children));
    }
}
