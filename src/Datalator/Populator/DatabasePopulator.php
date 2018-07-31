<?php

declare(strict_types = 1);

namespace Datalator\Populator;

use Datalator\Data\DataSourceInterface;
use Datalator\Popo\ModuleTable;
use Datalator\Popo\SchemaConfigurator;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class DatabasePopulator implements DatabasePopulatorInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;

    /**
     * @var \Datalator\Popo\SchemaConfigurator
     */
    protected $schemaConfigurator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        LoggerInterface $logger,
        Connection $databaseConnection,
        SchemaConfigurator $schemaConfigurator
    ) {
        $this->logger = $logger;
        $this->databaseConnection = $databaseConnection;
        $this->schemaConfigurator = $schemaConfigurator;
    }

    public function createDatabase(): void
    {
        $name = $this->schemaConfigurator
            ->requireDatabaseConfigurator()
            ->requireConnection()
            ->requireDbname();

        if ($this->databaseExists($name)) {
            $this->dropDatabase();
        }

        $this->getConnection()->getSchemaManager()->createDatabase(
            $name
        );

        $this->info('Created database');
    }

    public function dropDatabase(): void
    {
        $name = $this->schemaConfigurator
            ->requireDatabaseConfigurator()
            ->requireConnection()
            ->requireDbname();

        if (!$this->databaseExists($name)) {
            return;
        }

        $this->getConnection()->getSchemaManager()->dropDatabase(
            $name
        );

        $this->info('Dropped database');
    }

    public function populateDatabase(array $moduleCollection, array $data): void
    {
        foreach ($moduleCollection as $module) {
            $this->debug('Populating module: ' . $module->getName());
            foreach ($module->getTables() as $table) {
                /** @var \Datalator\Popo\ModuleTable $table */
                $dataSource = $this->getTableDataSource($module->getName(), $table->getName(), $data);
                $populator = $this->buildTablePopulator($table, $dataSource);

                $this->debug('Creating table: ' . $table->getName());
                $populator->createTable();

                $this->debug('Populating table: ' . $table->getName());
                $populator->populateTable();
            }
        }

        $this->info('Populated database');
    }

    /**
     * @param string $moduleName
     * @param string $tableName
     * @param array $data
     *
     * @throws \RuntimeException
     *
     * @return \Datalator\Data\DataSourceInterface
     */
    protected function getTableDataSource(string $moduleName, string $tableName, array $data): DataSourceInterface
    {
        if (!isset($data[$moduleName])) {
            throw new \RuntimeException('DataSource for module: ' . $moduleName . ' not found');
        }

        $dataCollection = $data[$moduleName];
        foreach ($dataCollection as $dataSource) {
            /** @var \Datalator\Data\DataSourceInterface $dataSource */
            $dataSourceName = $dataSource->getName();
            if (\strcasecmp($tableName, $dataSourceName) === 0) {
                return $dataSource;
            }
        }

        return null;
    }

    protected function buildTablePopulator(ModuleTable $table, DataSourceInterface $dataSource): TablePopulatorInterface
    {
        $populator = new TablePopulator(
            $this->getConnection(),
            $table,
            $dataSource
        );

        return $populator;
    }

    protected function getConnection(): Connection
    {
        return $this->databaseConnection;
    }

    protected function databaseExists(string $name): bool
    {
        try {
            return \in_array(
                $name,
                $this->getConnection()->getSchemaManager()->listDatabases()
            );
        } catch (\Throwable $e) {
            return false;
        }
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
}
