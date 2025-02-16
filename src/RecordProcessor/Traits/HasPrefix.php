<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use function RodrigoPedra\RecordProcessor\value_or_null;

trait HasPrefix
{
    protected ?string $prefix;

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = value_or_null($prefix);
    }
}
