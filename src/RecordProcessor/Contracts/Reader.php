<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use OuterIterator;

interface Reader extends OuterIterator, Resource
{
    public function getLineCount(): int;
}
