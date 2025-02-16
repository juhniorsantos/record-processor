<?php

namespace RodrigoPedra\RecordProcessor\Traits\BuilderConcerns;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Contracts\RecordParser;
use RodrigoPedra\RecordProcessor\Records\Parsers\ArrayRecordParser;
use RodrigoPedra\RecordProcessor\Records\Parsers\CallbackRecordParser;
use RodrigoPedra\RecordProcessor\Source;

trait BuildsSource
{
    protected RecordParser $recordParser;

    public function usingParser(callable|RecordParser $recordParser): static
    {
        if (is_callable($recordParser)) {
            $this->recordParser = new CallbackRecordParser($recordParser);

            return $this;
        }

        if (! $recordParser instanceof RecordParser) {
            throw new InvalidArgumentException('Parser should implement RecordParser interface');
        }

        $this->recordParser = $recordParser;

        return $this;
    }

    protected function getRecordParser(): RecordParser|ArrayRecordParser
    {
        if (is_null($this->recordParser)) {
            return new ArrayRecordParser;
        }

        return $this->recordParser;
    }

    protected function makeSource(): Source
    {
        $recordParser = $this->getRecordParser();

        return new Source($this->reader, $recordParser);
    }
}
