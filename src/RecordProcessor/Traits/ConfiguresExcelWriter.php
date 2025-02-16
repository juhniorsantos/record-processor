<?php

namespace RodrigoPedra\RecordProcessor\Traits;

use RodrigoPedra\RecordProcessor\Helpers\Writers\WriterConfigurator;

trait ConfiguresExcelWriter
{
    /** @var callable|null */
    protected $workbookConfigurator = null;

    /** @var callable|null */
    protected $worksheetConfigurator = null;

    public function getWorkbookConfigurator(): ?callable
    {
        if (is_null($this->workbookConfigurator)) {
            return null;
        }

        return function ($excel) {
            call_user_func_array($this->workbookConfigurator, [$excel]);
        };
    }

    public function setWorkbookConfigurator(callable $workbookConfigurator): void
    {
        $this->workbookConfigurator = $workbookConfigurator;
    }

    public function getWorksheetConfigurator(): ?callable
    {
        if (is_null($this->worksheetConfigurator)) {
            return null;
        }

        return function ($sheet) {
            call_user_func_array($this->worksheetConfigurator, [$sheet]);
        };
    }

    public function setWorksheetConfigurator(callable $worksheetConfigurator): void
    {
        $this->worksheetConfigurator = $worksheetConfigurator;
    }

    public function getConfigurableMethods(): array
    {
        return [
            'setWorkbookConfigurator',
            'setWorksheetConfigurator',
        ];
    }

    public function createConfigurator(): WriterConfigurator
    {
        return new WriterConfigurator($this, true, true);
    }
}
