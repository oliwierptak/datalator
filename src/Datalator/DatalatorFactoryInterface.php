<?php

declare(strict_types = 1);

namespace Datalator;

use Datalator\Builder\DatabaseBuilderInterface;
use Datalator\Data\Csv\CsvDataLoader;
use Datalator\Loader\Module\ModuleLoaderInterface;
use Datalator\Loader\Schema\SchemaLoaderInterface;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Reader\ReaderInterface;

interface DatalatorFactoryInterface
{
    public function getApplicationDirectory(): string;

    public function createDatabaseBuilder(LoaderConfigurator $configurator): DatabaseBuilderInterface;

    public function createSchemaLoader(): SchemaLoaderInterface;

    public function createModuleLoader(): ModuleLoaderInterface;

    public function createCsvDataLoader(): CsvDataLoader;

    public function createDatabaseReader(LoaderConfigurator $configurator): ReaderInterface;

    public function createCsvReader(LoaderConfigurator $configurator): ReaderInterface;
}
