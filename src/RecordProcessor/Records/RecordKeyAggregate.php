<?php

namespace RodrigoPedra\RecordProcessor\Records;

use BadMethodCallException;
use RodrigoPedra\RecordProcessor\Contracts\Record;
use RodrigoPedra\RecordProcessor\Contracts\RecordAggregate;

class RecordKeyAggregate implements RecordAggregate
{
    protected Record $master;

    protected array $records = [];

    public function __construct(Record $record)
    {
        $this->master = $record;

        $this->pushRecord($record);
    }

    public function get($key, $default = null): mixed
    {
        return $this->master->get($key, $default);
    }

    public function set($key, ?string $value): void
    {
        $this->master->set($key, $value);
    }

    public function getKey(): ?string
    {
        return $this->master->getKey();
    }

    public function valid(): bool
    {
        return $this->master->valid()
            && count($this->records) > 0;
    }

    public function pushRecord(Record $record): bool
    {
        if ($record->getKey() === $this->getKey()) {
            if ($record->valid()) {
                $this->records[] = $record;
            }

            return true;
        }

        return false;
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function toArray(): array
    {
        return [
            'master' => $this->master->toArray(),
            'records' => array_map(function (Record $record) {
                return $record->toArray();
            }, $this->records),
        ];
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->master, $method)) {
            return $this->master->{$method}(...$parameters);
        }

        $className = get_class($this->master);

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }
}
