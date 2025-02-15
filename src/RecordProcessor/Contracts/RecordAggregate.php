<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordAggregate extends Record
{
    public function pushRecord(Record $record): bool;
    public function getRecords(): array;
}
