<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use Psr\Log\LoggerInterface;

trait HasLogger
{
    protected LoggerInterface $logger;

    public function setLogger(?LoggerInterface $logger = null): void
    {
        $this->logger = $logger;
    }
}
