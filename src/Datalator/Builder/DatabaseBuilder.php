<?php

declare(strict_types = 1);

namespace Datalator\Builder;

use Datalator\Loader\DataLoaderInterface;
use Datalator\Loader\Schema\SchemaLoaderInterface;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\SchemaConfigurator;
use Datalator\Populator\DatabaseCreator;
use Datalator\Populator\DatabaseCreatorInterface;
use Datalator\Populator\DatabasePopulator;
use Datalator\Populator\DatabasePopulatorInterface;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class DatabaseBuilder implements DatabaseBuilderInterface
{
    /**
     * @var \Datalator\Popo\LoaderConfigurator
     */
    protected $loaderConfigurator;

    /**
     * @var \Datalator\Popo\SchemaConfigurator
     */
    protected $schemaConfigurator;

    /**
     * @var \Datalator\Loader\Schema\SchemaLoaderInterface
     */
    protected $schemaLoader;

    /**
     * @var \Datalator\Loader\DataLoaderInterface
     */
    protected $dataLoader;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        SchemaLoaderInterface $schemaLoader,
        DataLoaderInterface $dataLoader,
        LoggerInterface $logger,
        LoaderConfigurator $configurator
    ) {
        $this->schemaLoader = $schemaLoader;
        $this->dataLoader = $dataLoader;
        $this->logger = $logger;
        $this->loaderConfigurator = $configurator;
    }

    public function create(): void
    {
        $this
            ->buildDatabaseCreator()
            ->createDatabase();

        $this
            ->buildDatabasePopulator()
            ->populateSchema();
    }

    public function drop(): void
    {
        $this
            ->buildDatabaseCreator()
            ->dropDatabase();
    }

    public function populate(): void
    {
        $this->drop();
        $this->create();

        $data = $this->dataLoader->load($this->loaderConfigurator);

        $this
            ->buildDatabasePopulator()
            ->populateData(
                $this->getSchemaConfigurator()->requireLoadedModules(),
                $data
            );
    }

    public function import(array $importConfiguratorCollection): void
    {
        $this->populate();
        $populator = $this->buildDatabasePopulator();

        foreach ($importConfiguratorCollection as $importConfigurator) {
            $schemaConfigurator = $this->schemaLoader->load($importConfigurator);
            $data = $this->dataLoader->load($importConfigurator);

            $populator->populateSchema($schemaConfigurator);
            $populator->populateData($schemaConfigurator->requireLoadedModules(), $data);
        }

        $this->info('Imported');
    }

    public function databaseExists(string $name): bool
    {
        $connection = $this->buildConnection(false);

        try {
            return \in_array(
                $name,
                $connection->getSchemaManager()->listDatabases()
            );
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function info(string $info): void
    {
        $this->logger->info($this->formatInfo($info));
    }

    protected function formatInfo(string $info): string
    {
        return \sprintf(
            '%s (%s:%s)',
            $info,
            $this->schemaConfigurator
                ->requireDatabaseConfigurator()
                ->requireConnection()
                ->requireDbname(),
            $this->schemaConfigurator->requireSchemaName()
        );
    }

    protected function buildConnection(bool $useDatabase): Connection
    {
        $config = $this
            ->getSchemaConfigurator()
            ->requireDatabaseConfigurator()
            ->requireConnection()
            ->toArray();

        if (!$useDatabase) {
            unset($config['dbname']);
        }

        $databaseConnection = \Doctrine\DBAL\DriverManager::getConnection(
            $config
        );

        return $databaseConnection;
    }

    protected function getSchemaConfigurator(): SchemaConfigurator
    {
        if ($this->schemaConfigurator === null) {
            $this->schemaConfigurator = $this->schemaLoader->load(
                $this->loaderConfigurator
            );
        }

        return $this->schemaConfigurator;
    }

    protected function buildDatabaseCreator(?LoaderConfigurator $configurator = null): DatabaseCreatorInterface
    {
        if ($configurator === null) {
            $configurator = clone $this->loaderConfigurator;
        }

        $schemaConfigurator = $this->schemaLoader->load(
            $configurator
        );

        return new DatabaseCreator(
            $this->logger,
            $this->buildConnection(false),
            $schemaConfigurator
        );
    }

    protected function buildDatabasePopulator(?LoaderConfigurator $configurator = null): DatabasePopulatorInterface
    {
        if ($configurator === null) {
            $configurator = clone $this->loaderConfigurator;
        }

        $schemaConfigurator = $this->schemaLoader->load(
            $configurator
        );

        return new DatabasePopulator(
            $this->logger,
            $this->buildConnection(true),
            $schemaConfigurator
        );
    }
}
