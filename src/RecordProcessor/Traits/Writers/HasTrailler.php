<?php

namespace RodrigoPedra\RecordProcessor\Traits\Writers;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Exceptions\InvalidAddonException;
use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterAddon;

trait HasTrailler
{
    protected ?WriterAddon $trailler = null;

    public function getTrailler(): ?WriterAddon
    {
        return $this->trailler;
    }

    public function setTrailler($trailler): static
    {
        if (is_null($trailler)) {
            return $this;
        }

        try {
            $this->trailler = new WriterAddon($trailler);
        } catch (InvalidAddonException $ex) {
            throw new InvalidArgumentException('Writer header should be an array or a callable');
        }

        return $this;
    }
}
