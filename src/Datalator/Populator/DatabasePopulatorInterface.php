<?php

declare(strict_types = 1);

namespace Datalator\Populator;

use Datalator\Popo\SchemaConfigurator;

interface DatabasePopulatorInterface
{
    public function populateSchema(?SchemaConfigurator $schemaConfigurator = null): void;

    /**
     * @param \Datalator\Popo\ModuleConfigurator[] $moduleCollection
     * @param array $data
     *
     * @return void
     */
    public function populateData(array $moduleCollection, array $data): void;
}
