<?php

declare(strict_types = 1);

namespace Datalator\Helper;

use Datalator\Popo\ReaderConfigurator;
use Datalator\Popo\ReaderValue;
use Datalator\Populator\DatabaseCreatorInterface;
use Datalator\Populator\DatabasePopulatorInterface;

class TestPopulatorHelper
{
    use TraitHelper;

    /**
     * @var \Datalator\Loader\Schema\SchemaLoaderInterface
     */
    protected $schemaLoader;

    /**
     * @var \Datalator\Popo\SchemaConfigurator
     */
    protected $schemaConfigurator;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connectionCreator;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connectionPopulator;

    /**
     * @var \Datalator\Loader\DataLoaderInterface
     */
    protected $dataLoader;

    /**
     * @var \Datalator\Reader\ReaderInterface
     */
    protected $databaseReader;

    protected function build(): self
    {
        if ($this->isDone) {
            return $this;
        }

        $this->configure();

        $this->schemaLoader = $this->getFactory()->createSchemaLoader();
        $this->schemaConfigurator = $this->schemaLoader->load($this->configurator);

        $this->connectionCreator = $this->getFactory()->createConnection($this->configurator, false);
        $this->connectionPopulator = $this->getFactory()->createConnection($this->configurator, true);

        $this->dataLoader = $this->getFactory()->createCsvDataLoader();

        $this->databaseReader = $this->getFactory()->createDatabaseReaderFromConnection(
            $this->connectionPopulator
        );

        $this->done();

        return $this;
    }

    protected function buildDatabaseCreator(): DatabaseCreatorInterface
    {
        return $this->getFactory()->createDatabaseCreator(
            $this->connectionCreator,
            $this->schemaConfigurator
        );
    }

    protected function buildDatabasePopulator(): DatabasePopulatorInterface
    {
        return $this->getFactory()->createDatabasePopulator(
            $this->connectionPopulator,
            $this->schemaConfigurator
        );
    }

    public function readValue(ReaderConfigurator $configurator): ReaderValue
    {
        $this->build();

        return $this->databaseReader->read($configurator);
    }

    public function populate(): self
    {
        $this->build();

        $this->buildDatabaseCreator()->dropDatabase();
        $this->buildDatabaseCreator()->createDatabase();

        $this
            ->buildDatabasePopulator()
            ->populateSchema();

        $data = $this->dataLoader->load($this->configurator);

        $this->connectionPopulator->beginTransaction();

        $this
            ->buildDatabasePopulator()
            ->populateData(
                $this->schemaConfigurator->requireLoadedModules(),
                $data
            );

        return $this;
    }

    public function rollback(): self
    {
        $this->build();

        if ($this->connectionPopulator->isConnected() && $this->connectionPopulator->isTransactionActive()) {
            $this->connectionPopulator->rollBack();
        }

        return $this;
    }

    public function commit(): self
    {
        $this->build();

        if ($this->connectionPopulator->isConnected() && $this->connectionPopulator->isTransactionActive()) {
            $this->connectionPopulator->commit();
        }


        return $this;
    }
}
