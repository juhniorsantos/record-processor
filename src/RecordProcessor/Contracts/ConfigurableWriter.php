<?php

namespace RodrigoPedra\RecordProcessor\Contracts;

use RodrigoPedra\RecordProcessor\Helpers\Configurator;

interface ConfigurableWriter extends Configurable, Writer
{
    public function createConfigurator(): Configurator;
}
