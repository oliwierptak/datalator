<?php

declare(strict_types = 1);

namespace Datalator;

use Datalator\Builder\DatabaseBuilderInterface;
use Datalator\Data\Csv\CsvDataLoader;
use Datalator\Loader\Module\ModuleLoaderInterface;
use Datalator\Loader\Schema\SchemaLoaderInterface;
use Datalator\Popo\LoaderConfigurator;

interface DatalatorFactoryInterface
{
    public function getApplicationDirectory(): string;

    public function createDatabaseBuilder(LoaderConfigurator $configurator): DatabaseBuilderInterface;

    public function createSchemaLoader(): SchemaLoaderInterface;

    public function createModuleLoader(): ModuleLoaderInterface;

    public function createCsvDataLoader(): CsvDataLoader;
}
