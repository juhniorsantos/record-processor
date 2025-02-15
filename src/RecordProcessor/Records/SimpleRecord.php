<?php

namespace RodrigoPedra\RecordProcessor\Records;

use Illuminate\Support\Fluent;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\JsonRecord;
use RodrigoPedra\RecordProcessor\Contracts\TextRecord;
use RodrigoPedra\RecordProcessor\Writers\JSONFileWriter;

class SimpleRecord extends Fluent implements Record, TextRecord
{
    public function get($key, $default = '')
    {
        return parent::get($key, $default);
    }

    public function set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function valid(): bool
    {
        return $this->getKey() != '';
    }

    public function getKey(): ?string
    {
        return reset($this->attributes) ?: '';
    }

    public function toText(): string
    {
        return $this->toJson(JSONFileWriter::JSON_ENCODE_OPTIONS);
    }
}
