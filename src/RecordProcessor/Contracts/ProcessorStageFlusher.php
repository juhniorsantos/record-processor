<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Stages\TransferObjects\FlushPayload;

interface ProcessorStageFlusher extends ProcessorStage
{
    public function flush(FlushPayload $payload): mixed;
}
