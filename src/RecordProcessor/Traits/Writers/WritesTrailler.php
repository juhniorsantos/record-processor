<?php

namespace RodrigoPedra\RecordProcessor\Traits\Writers;

trait WritesTrailler
{
    protected function writeTrailler(): void
    {
        $trailler = $this->getTrailler();

        if (is_null($trailler)) {
            return;
        }

        $trailler->handle($this->writer, $this->getRecordCount());
    }
}
