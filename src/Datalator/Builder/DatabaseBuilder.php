<?php

declare(strict_types = 1);

namespace Datalator\Builder;

use Datalator\Loader\DataLoaderInterface;
use Datalator\Loader\LoaderValidatorInterface;
use Datalator\Loader\Schema\SchemaLoaderInterface;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\SchemaConfigurator;
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
     * @var \Datalator\Loader\LoaderValidatorInterface
     */
    protected $validator;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        SchemaLoaderInterface $schemaLoader,
        DataLoaderInterface $dataLoader,
        LoaderValidatorInterface $validator,
        LoggerInterface $logger,
        LoaderConfigurator $configurator
    ) {
        $this->schemaLoader = $schemaLoader;
        $this->dataLoader = $dataLoader;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->loaderConfigurator = $configurator;
    }

    public function __destruct()
    {
        if (!$this->databaseConnection || !$this->databaseConnection->isConnected()) {
            return;
        }

        $this->databaseConnection->close();
    }

    public function create(): void
    {
        $this->validator->validate($this->loaderConfigurator);

        $this
            ->buildDatabaseCreator()
            ->createDatabase();

        //rebuild connection next time with use database = true
        $this->databaseConnection->close();
        $this->databaseConnection = null;
    }

    public function drop(): void
    {
        $this->validator->validate($this->loaderConfigurator);

        $this
            ->buildDatabasePopulator()
            ->dropDatabase();

        $this->databaseConnection = null;
    }

    public function populate(): void
    {
        $this->validator->validate($this->loaderConfigurator);

        $this->drop();
        $this->create();

        $data = $this->dataLoader->load($this->loaderConfigurator);

        $this->populateDatabase(
            $this->getSchemaConfigurator()->requireLoadedModules(),
            $data
        );
    }

    public function import(array $importConfiguratorCollection): void
    {
        $this->populate();

        foreach ($importConfiguratorCollection as $importConfigurator) {
            $schemaConfigurator = $this->schemaLoader->load($importConfigurator);
            $data = $this->dataLoader->load($importConfigurator);

            $this->populateDatabase(
                $schemaConfigurator->requireLoadedModules(),
                $data
            );
        }

        $this->info('Imported');
    }

    public function databaseExists(string $name): bool
    {
        return $this
            ->buildDatabasePopulator()
            ->databaseExists($name);
    }

    protected function info(string $info): void
    {
        $this->logger->info($this->formatInfo($info));
    }

    protected function debug(string $info): void
    {
        $this->logger->debug($this->formatInfo($info));
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

    /**
     * @param \Datalator\Popo\ModuleConfigurator[] $moduleCollection
     * @param array $data
     *
     * @return void
     */
    protected function populateDatabase(array $moduleCollection, array $data): void
    {
        $this
            ->buildDatabasePopulator()
            ->populateDatabase($moduleCollection, $data);
    }

    protected function buildConnection(bool $useDatabase): Connection
    {
        if ($this->databaseConnection === null) {
            $config = $this
                ->getSchemaConfigurator()
                ->requireDatabaseConfigurator()
                ->requireConnection()
                ->toArray();

            if (!$useDatabase) {
                unset($config['dbname']);
            }

            $this->databaseConnection = \Doctrine\DBAL\DriverManager::getConnection(
                $config
            );
        }

        return $this->databaseConnection;
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

    protected function buildDatabaseCreator(): DatabasePopulatorInterface
    {
        return new DatabasePopulator(
            $this->logger,
            $this->buildConnection(false),
            $this->getSchemaConfigurator()
        );
    }

    protected function buildDatabasePopulator(): DatabasePopulatorInterface
    {
        return new DatabasePopulator(
            $this->logger,
            $this->buildConnection(true),
            $this->getSchemaConfigurator()
        );
    }
}
