<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use InvalidArgumentException;
use RodrigoPedra\RecordProcessor\Contracts\NewLines;

/**
 *  A trait to configure and check CSV file and content
 * Partially extracted from
 * \League\Csv\Config\AbstractCsv
 *
 * @license http://opensource.org/licenses/MIT
 *
 * @link    https://github.com/thephpleague/csv/
 *
 * @version 9.0.1
 */
trait CsvControls
{
    protected string $delimiter = ',';

    protected string $enclosure = '"';

    protected string $escape = '\\';

    protected string $newline = NewLines::UNIX_NEWLINE;

    protected string $outputBOM = '';

    public function setDelimiter(string $delimiter): void
    {
        if (! $this->isValidCsvControls($delimiter)) {
            throw new InvalidArgumentException('The delimiter must be a single character');
        }
        $this->delimiter = $delimiter;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function setEnclosure(string $enclosure): void
    {
        if (! $this->isValidCsvControls($enclosure)) {
            throw new InvalidArgumentException('The enclosure must be a single character');
        }
        $this->enclosure = $enclosure;
    }

    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    public function setEscape(string $escape): void
    {
        if (! $this->isValidCsvControls($escape)) {
            throw new InvalidArgumentException('The escape character must be a single character');
        }
        $this->escape = $escape;
    }

    public function getEscape(): string
    {
        return $this->escape;
    }

    public function setNewline(string $newline): void
    {
        $this->newline = (string) $newline;
    }

    public function getNewline(): string
    {
        return $this->newline;
    }

    public function setOutputBOM(string $str): void
    {
        $this->outputBOM = (string) $str;
    }

    public function getOutputBOM(): string
    {
        return $this->outputBOM;
    }

    protected function isValidCsvControls(string $str): bool
    {
        return mb_strlen($str) === 1;
    }
}
