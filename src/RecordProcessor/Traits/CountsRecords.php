<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait CountsRecords
{
    protected int $recordCount = 0;

    public function getRecordCount(): int
    {
        return $this->recordCount;
    }

    protected function incrementRecordCount($amount = 1): void
    {
        $this->recordCount += $amount;
    }
}
