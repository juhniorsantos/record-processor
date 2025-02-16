<?php

namespace RodrigoPedra\RecordProcessor\Records;

use Illuminate\Support\Fluent;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;

class SimpleRecord extends Fluent implements Record, TextRecord
{
    public function get($key, $default = ''): mixed
    {
        return parent::get($key, $default);
    }

    public function set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function valid(): bool
    {
        return $this->getKey() !== '';
    }

    public function getKey(): ?string
    {
        return reset($this->attributes) ?: '';
    }

    public function toText(): string
    {
        return implode(', ', $this->toArray());
    }
}
