<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

interface Writer extends Resource
{
    public function append(mixed $content): void;
    public function getLineCount(): int;
    public function output(): mixed;
}
