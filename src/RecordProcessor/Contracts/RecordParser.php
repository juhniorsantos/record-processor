<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordParser
{
    /**
     * Generates Record objects from raw data
     */
    public function parseRecord(Reader $reader, $rawContent): Record;
}
