<?php

declare(strict_types = 1);

namespace Datalator\Populator;

use Datalator\Popo\SchemaConfigurator;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class DatabaseCreator implements DatabaseCreatorInterface
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
        if ($this->databaseExists()) {
            $this->dropDatabase();
        }

        $name = $this->resolveDatabaseName();
        $name = $this
            ->getConnection()
            ->getSchemaManager()
            ->getDatabasePlatform()
            ->quoteSingleIdentifier($name);

        $this->getConnection()->getSchemaManager()->createDatabase(
            $name
        );

        $this->info('Created database');
    }

    public function dropDatabase(): void
    {
        if (!$this->databaseExists()) {
            return;
        }

        $name = $this->resolveDatabaseName();
        $name = $this
            ->getConnection()
            ->getSchemaManager()
            ->getDatabasePlatform()
            ->quoteSingleIdentifier($name);

        $this->getConnection()->getSchemaManager()->dropDatabase(
            $name
        );

        $this->info('Dropped database');
    }

    public function databaseExists(): bool
    {
        $name = $this->resolveDatabaseName();

        try {
            return \in_array(
                $name,
                $this->getConnection()->getSchemaManager()->listDatabases()
            );
        }
        catch (\Throwable $e) {
            return false;
        }
    }

    protected function getConnection(): Connection
    {
        return $this->databaseConnection;
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

    protected function resolveDatabaseName(): string
    {
        return $this->schemaConfigurator
            ->requireDatabaseConfigurator()
            ->requireConnection()
            ->requireDbname();
    }
}
