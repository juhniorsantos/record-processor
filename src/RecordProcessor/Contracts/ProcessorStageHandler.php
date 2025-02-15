<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface ProcessorStageHandler extends ProcessorStage
{
    public function handle(Record $record): ?Record;
}
