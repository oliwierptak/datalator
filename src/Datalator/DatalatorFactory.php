<?php

declare(strict_types = 1);

namespace Datalator;

use Datalator\Builder\DatabaseBuilder;
use Datalator\Builder\DatabaseBuilderInterface;
use Datalator\Data\Csv\CsvDataLoader;
use Datalator\Finder\FileLoaderInterface;
use Datalator\Finder\FinderFactory;
use Datalator\Finder\FinderFactoryInterface;
use Datalator\Loader\LoaderValidator;
use Datalator\Loader\LoaderValidatorInterface;
use Datalator\Loader\Module\ModuleLoader;
use Datalator\Loader\Module\ModuleLoaderInterface;
use Datalator\Loader\Schema\SchemaLoader;
use Datalator\Loader\Schema\SchemaLoaderInterface;
use Datalator\Logger\LoggerFactory;
use Datalator\Logger\LoggerFactoryInterface;
use Datalator\Popo\LoaderConfigurator;
use Psr\Log\LoggerInterface;

class DatalatorFactory implements DatalatorFactoryInterface
{
    public function getApplicationDirectory(): string
    {
        return \rtrim(\getcwd(), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
    }

    public function createDatabaseBuilder(LoaderConfigurator $configurator): DatabaseBuilderInterface
    {
        return new DatabaseBuilder(
            $this->createSchemaLoader(),
            $this->createCsvDataLoader(),
            $this->createLoaderValidator(),
            $this->createLogger(),
            $configurator
        );
    }

    public function createSchemaLoader(): SchemaLoaderInterface
    {
        return new SchemaLoader(
            $this->createFileLoader(),
            $this->createModuleLoader()
        );
    }

    public function createModuleLoader(): ModuleLoaderInterface
    {
        return new ModuleLoader(
            $this->createFileLoader(),
            $this->createLogger()
        );
    }

    public function createCsvDataLoader(): CsvDataLoader
    {
        return new CsvDataLoader(
            $this->createFileLoader()
        );
    }

    protected function createFinderFactory(): FinderFactoryInterface
    {
        return new FinderFactory();
    }

    protected function createFileLoader(): FileLoaderInterface
    {
        return $this->createFinderFactory()
            ->createFileLoader();
    }

    protected function createLoaderValidator(): LoaderValidatorInterface
    {
        return new LoaderValidator();
    }

    public function createLogger(): LoggerInterface
    {
        $loggerConfigurator = $this->createLoggerFactory()->createConfigurator(
            $this->getApplicationDirectory() . 'logs/datalator'
        );

        return $this
            ->createLoggerFactory()
            ->createLogger($loggerConfigurator);
    }

    protected function createLoggerFactory(): LoggerFactoryInterface
    {
        return new LoggerFactory();
    }
}
