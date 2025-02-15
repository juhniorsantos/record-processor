<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use OuterIterator;

interface Reader extends Resource, OuterIterator
{
    public function getLineCount(): int;
}
