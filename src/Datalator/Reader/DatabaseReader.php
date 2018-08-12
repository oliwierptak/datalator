<?php

declare(strict_types = 1);

namespace Datalator\Reader;

use Datalator\Popo\ReaderConfigurator;
use Datalator\Popo\ReaderValue;
use Datalator\Popo\SchemaConfigurator;
use Doctrine\DBAL\Connection;

class DatabaseReader implements ReaderInterface
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
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Datalator\Loader\Schema\SchemaLoaderInterface $schemaLoader,
        \Psr\Log\LoggerInterface $logger,
        \Datalator\Popo\LoaderConfigurator $loaderConfigurator
    ) {
        $this->schemaLoader = $schemaLoader;
        $this->logger = $logger;
        $this->loaderConfigurator = $loaderConfigurator;
    }

    public function read(ReaderConfigurator $configurator): ReaderValue
    {
        $value = (new ReaderValue());

        $sql = \sprintf(
            "SELECT %s FROM %s WHERE %s = '%s' LIMIT 1",
            $configurator->getQueryColumn(),
            $configurator->getSource(),
            $configurator->getIdentityColumn(),
            $configurator->getIdentityValue()
        );

        $data = $this->getConnection()->fetchAssoc($sql);
        if (!$data) {
            return $value;
        }

        if ($configurator->getQueryColumn() === '*') {
            $value->setSchemaValue($data);
        } else {
            $value->setSchemaValue($data[$configurator->getQueryColumn()]);
        }

        return $value;
    }

    protected function getConnection(): Connection
    {
        if ($this->databaseConnection === null) {
            $config = $this
                ->getSchemaConfigurator()
                ->requireDatabaseConfigurator()
                ->requireConnection()
                ->toArray();

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
}
