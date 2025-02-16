<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use Exception;
use RuntimeException;

class PDOBufferedWriter extends PDOWriter
{
    const int BUFFER_LIMIT = 100;

    protected array $buffer = [];

    /**
     * @throws Exception
     */
    public function close(): void
    {
        $this->flush();

        parent::close();
    }

    public function append(mixed $content): void
    {
        if (! is_array($content)) {
            throw new RuntimeException('content for PDOBufferedWriter should be an array');
        }

        $this->pushValues($content);
    }

    /**
     * @throws Exception
     */
    public function pushValues(array $values): void
    {
        $count = array_push($this->buffer, $values);

        if ($count === static::BUFFER_LIMIT) {
            $this->flush();
        }
    }

    /**
     * @throws Exception
     */
    public function flush(): void
    {
        $count = count($this->buffer);

        if ($count === 0) {
            return;
        }

        try {
            $data = $this->flushData();
            $writer = $this->prepareWriter($count);

            if (! $writer->execute($data)) {
                throw new RuntimeException('Could not write PDO records');
            }

            $this->incrementLineCount($writer->rowCount());
        } catch (Exception $exception) {
            if ($this->inTransaction) {
                $this->pdo->rollBack();
                $this->inTransaction = false;
            }

            throw $exception;
        } finally {
            $data = null;
        }
    }

    protected function prepareWriter($count): false|\PDOStatement|null
    {
        if ($count !== static::BUFFER_LIMIT) {
            $this->writer = null;
        }

        return parent::prepareWriter($count);
    }

    protected function flushData(): array
    {
        $result = [];

        foreach ($this->buffer as $values) {
            $values = $this->prepareValuesForInsert($values);

            array_push($result, ...$values);
        }

        $this->buffer = [];

        return $result;
    }
}
