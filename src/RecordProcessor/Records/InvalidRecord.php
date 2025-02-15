<?php

namespace RodrigoPedra\RecordProcessor\Records;

use RodrigoPedra\RecordProcessor\Contracts\Record;

class InvalidRecord implements Record
{
    public function get(string $field, ?string $default = null)
    {
        return '';
    }

    public function set(string $field, ?string $value)
    {
        //
    }

    public function getKey()
    {
        return '';
    }

    public function valid()
    {
        return false;
    }

    public function toArray()
    {
        return [];
    }
}
