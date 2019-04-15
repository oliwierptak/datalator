<?php

declare(strict_types = 1);

namespace Datalator;

use Datalator\Builder\DatabaseBuilderInterface;
use Datalator\Data\Csv\CsvDataLoader;
use Datalator\Loader\LoaderValidatorInterface;
use Datalator\Loader\Module\ModuleLoaderInterface;
use Datalator\Loader\Schema\SchemaLoaderInterface;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\SchemaConfigurator;
use Datalator\Populator\DatabaseCreatorInterface;
use Datalator\Populator\DatabasePopulatorInterface;
use Datalator\Reader\ReaderInterface;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

interface DatalatorFactoryInterface
{
    public function getApplicationDirectory(): string;

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param bool $useDatabase
     *
     * @return \Doctrine\DBAL\Connection
     *
     * @throws \InvalidArgumentException In case when validation fails
     */
    public function createConnection(LoaderConfigurator $configurator, bool $useDatabase): Connection;

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param bool $useDatabase
     *
     * @return \Pdo
     *
     * @throws \InvalidArgumentException In case when validation fails
     */
    public function createPdo(LoaderConfigurator $configurator, bool $useDatabase): \Pdo;

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @return \Datalator\Builder\DatabaseBuilderInterface
     *
     * @throws \InvalidArgumentException In case when validation fails
     */
    public function createDatabaseBuilder(LoaderConfigurator $configurator): DatabaseBuilderInterface;

    public function createSchemaLoader(): SchemaLoaderInterface;

    public function createModuleLoader(): ModuleLoaderInterface;

    public function createCsvDataLoader(): CsvDataLoader;

    public function createDatabaseReader(LoaderConfigurator $configurator): ReaderInterface;

    public function createDatabaseReaderFromConnection(Connection $connection): ReaderInterface;

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @return \Datalator\Reader\ReaderInterface
     *
     * @throws \InvalidArgumentException In case when configurator's validation fails
     */
    public function createCsvReader(LoaderConfigurator $configurator): ReaderInterface;

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param \Doctrine\DBAL\Connection|null $connection
     *
     * @return \Datalator\Populator\DatabasePopulatorInterface
     *
     * @throws \InvalidArgumentException In case when configurator's validation fails
     */
    //public function createDatabasePopulator(LoaderConfigurator $configurator, ?Connection $connection = null): DatabasePopulatorInterface;

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @return \Datalator\Popo\SchemaConfigurator
     *
     * @throws \InvalidArgumentException In case when configurator's validation fails
     */
    public function createSchemaConfigurator(LoaderConfigurator $configurator): SchemaConfigurator;

    public function createDatabaseCreator(Connection $connection, SchemaConfigurator $schemaConfigurator): DatabaseCreatorInterface;

    public function createDatabasePopulator(Connection $connection, SchemaConfigurator $schemaConfigurator): DatabasePopulatorInterface;

    public function createLoaderValidator(): LoaderValidatorInterface;

    public function createLogger(): LoggerInterface;
}
