<?php

namespace RodrigoPedra\RecordProcessor\Writers;

use RodrigoPedra\RecordProcessor\Contracts\Writer;

class ArrayWriter implements Writer
{
    /** @var array */
    protected $items = [];

    public function open()
    {
        $this->items = [];
    }

    public function close()
    {
        //
    }

    public function append(mixed $content)
    {
        array_push($this->items, $content);
    }

    /**
     * @return  int
     */
    public function getLineCount()
    {
        return count($this->items);
    }

    /**
     * @return mixed
     */
    public function output()
    {
        return $this->items;
    }
}
