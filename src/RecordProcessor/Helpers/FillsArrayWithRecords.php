<?php

namespace RodrigoPedra\RecordProcessor\Helpers;

use RodrigoPedra\RecordProcessor\Contracts\Record;

/**
 * Trait FillsArrayWithRecords
 * Use with a \RodrigoPedra\RecordProcessor\Contracts\RecordAggregate implementation
 */
trait FillsArrayWithRecords
{
    /**
     * @param  int  $offset
     * @return int returns the record size
     */
    abstract protected function fillArrayWithSingleRecord(array &$results, Record $record, $offset);

    /**
     * @param  Record[]  $records
     * @param  int  $limit
     * @param  int  $offset
     * @return void
     */
    protected function fillArrayWithRecords(array &$results, array $records, $limit, $offset)
    {
        $length = min(count($records), $limit);

        $index = 0;

        foreach ($records as $record) {
            $offset += $this->fillArrayWithSingleRecord($results, $record, $offset);

            $index++;

            if ($index >= $length) {
                break;
            }
        }
    }
}
