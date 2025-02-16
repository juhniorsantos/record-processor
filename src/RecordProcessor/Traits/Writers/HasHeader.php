<?php

namespace RodrigoPedra\RecordProcessor\Traits\Writers;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterAddon;

trait HasHeader
{
    protected ?WriterAddon $header = null;

    public function getHeader(): ?WriterAddon
    {
        return $this->header;
    }

    public function setHeader($header): static
    {
        if (is_null($header)) {
            return $this;
        }

        try {
            $this->header = new WriterAddon($header);
        } catch (InvalidAddonException $ex) {
            throw new InvalidArgumentException('Writer header should be an array or a callable');
        }

        return $this;
    }
}
