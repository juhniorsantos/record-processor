<?php

namespace RodrigoPedra\RecordProcessor\Records;

use RodrigoPedra\RecordProcessor\Contracts\Record;

class InvalidRecord implements Record
{
    public function get($key, $default = null): string
    {
        return '';
    }

    public function set($key, $value): void
    {
        //
    }

    public function getKey(): ?string
    {
        return '';
    }

    public function valid(): bool
    {
        return false;
    }

    public function toArray(): array
    {
        return [];
    }
}
