<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use Illuminate\Contracts\Support\Arrayable;

interface Record extends Arrayable
{
    public function get($key, $default = '');
    public function set(string $key, ?string $value): void;
    public function getKey(): ?string;
    public function valid(): bool;
}
