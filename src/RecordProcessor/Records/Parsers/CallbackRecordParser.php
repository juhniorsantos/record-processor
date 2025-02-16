<?php

namespace RodrigoPedra\RecordProcessor\Records\Parsers;

use RodrigoPedra\RecordProcessor\Contracts\Reader;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;

class CallbackRecordParser implements RecordParser
{
    /** @var callable */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function parseRecord(Reader $reader, $rawContent): Record
    {
        return call_user_func_array($this->callback, [$rawContent]);
    }
}
