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
use Datalator\Popo\SchemaConfigurator;
use Datalator\Reader\CsvReader;
use Datalator\Reader\DatabaseReader;
use Datalator\Reader\ReaderInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PDO;
use Psr\Log\LoggerInterface;

class DatalatorFactory implements DatalatorFactoryInterface
{
    public function getApplicationDirectory(): string
    {
        return \rtrim(\getcwd(), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
    }

    public function createConnection(LoaderConfigurator $configurator, bool $useDatabase): Connection
    {
        $this->createLoaderValidator()->validate($configurator);

        $schemaConfigurator = $this->createSchemaLoader()
            ->load($configurator);

        $config = $schemaConfigurator
            ->requireDatabaseConfigurator()
            ->requireConnection()
            ->toArray();

        if (!$useDatabase) {
            unset($config['dbname']);
        }

        $connection = DriverManager::getConnection($config);

        return $connection;
    }

    public function createPdo(LoaderConfigurator $configurator, bool $useDatabase): Pdo
    {
        $this->createLoaderValidator()->validate($configurator);

        $schemaConfigurator = $this->createSchemaLoader()
            ->load($configurator);

        //$dsn = "<driver>://<username>:<password>@<host>:<port>/<database>";
        //mysql://user:secret@localhost/dbname
        //mysql:host=hostname;dbname=databasename
        $dsn = \sprintf(
            '%s:host=%s:%s;dbname=%s',
            'mysql', //$schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requireDriver(),
            $schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requireHost(),
            $schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requirePort(),
            $schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requireDbname()
        );

        if (!$useDatabase) {
            $dsn = \sprintf(
                '%s:host=%s:%s',
                'mysql', //$schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requireDriver(),
                $schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requireHost(),
                $schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requirePort()
            );
        }

        $connection = new PDO(
            $dsn,
            $schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requireUser(),
            $schemaConfigurator->requireDatabaseConfigurator()->requireConnection()->requirePassword()
        );

        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }

    public function createDatabaseBuilder(LoaderConfigurator $configurator): DatabaseBuilderInterface
    {
        $this->createLoaderValidator()->validate($configurator);

        return new DatabaseBuilder(
            $this->createSchemaLoader(),
            $this->createCsvDataLoader(),
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

    public function createSchemaConfigurator(LoaderConfigurator $configurator): SchemaConfigurator
    {
        $this->createLoaderValidator()->validate($configurator);

        return $this->createSchemaLoader()->load(
            $configurator
        );
    }

    public function createDatabaseReader(LoaderConfigurator $configurator): ReaderInterface
    {
        $this->createLoaderValidator()->validate($configurator);
        $connection = $this->createConnection($configurator, true);

        return new DatabaseReader($connection);
    }

    public function createDatabaseReaderFromConnection(Connection $connection): ReaderInterface
    {
        return new DatabaseReader($connection);
    }

    public function createCsvReader(LoaderConfigurator $configurator): ReaderInterface
    {
        $this->createLoaderValidator()->validate($configurator);

        return new CsvReader(
            $this->createCsvDataLoader(),
            $this->createLogger(),
            $configurator
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

    public function createLoaderValidator(): LoaderValidatorInterface
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
