<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface RecordFormatter
{
    /**
     * Encode Record objects content to writer format
     */
    public function formatRecord(Writer $writer, Record $record): bool;
}
