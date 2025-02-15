<?php

namespace RodrigoPedra\RecordProcessor\Traits;

trait CountsLines
{
    protected int $lineCount = 0;

    public function getLineCount(): int
    {
        return $this->lineCount;
    }

    protected function incrementLineCount($amount = 1): void
    {
        $this->lineCount += $amount;
    }
}
