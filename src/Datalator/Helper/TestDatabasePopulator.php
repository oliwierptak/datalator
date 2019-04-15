<?php

declare(strict_types = 1);

namespace Datalator\Helper;

use Datalator\Popo\ReaderConfigurator;
use Datalator\Popo\ReaderValue;
use Datalator\Populator\DatabaseCreatorInterface;
use Datalator\Populator\DatabasePopulatorInterface;

class TestDatabasePopulator
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
    protected $connectionCreate;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connectionPopulate;

    /**
     * @var \Datalator\Loader\DataLoaderInterface
     */
    protected $dataLoader;

    /**
     * @var \Datalator\Reader\ReaderInterface
     */
    protected $databaseReader;

    /**
     * @var \Datalator\Populator\DatabaseCreatorInterface
     */
    protected $databaseCreator;

    protected function build(): self
    {
        if ($this->isDone) {
            return $this;
        }

        $this->configure();

        $this->schemaLoader = $this->getFactory()->createSchemaLoader();
        $this->schemaConfigurator = $this->schemaLoader->load($this->configurator);

        $this->connectionCreate = $this->getFactory()->createConnection($this->configurator, false);
        $this->connectionPopulate = $this->getFactory()->createConnection($this->configurator, true);

        $this->dataLoader = $this->getFactory()->createCsvDataLoader();

        $this->databaseReader = $this->getFactory()->createDatabaseReaderFromConnection($this->connectionPopulate);
        $this->databaseCreator = $this->getFactory()->createDatabaseCreator($this->connectionCreate, $this->schemaConfigurator);

        $this->done();

        return $this;
    }

    protected function buildDatabaseCreator(): DatabaseCreatorInterface
    {
        return $this->getFactory()->createDatabaseCreator(
            $this->connectionCreate,
            $this->schemaConfigurator
        );
    }

    protected function buildDatabasePopulator(): DatabasePopulatorInterface
    {
        return $this->getFactory()->createDatabasePopulator(
            $this->connectionPopulate,
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

        $this->databaseCreator->dropDatabase();
        $this->databaseCreator->createDatabase();

        //$this->connectionPopulate = $this->getFactory()->createConnection($this->configurator, true);

        $this
            ->buildDatabasePopulator()
            ->populateSchema();

        $data = $this->dataLoader->load($this->configurator);

        $this->connectionPopulate->beginTransaction();

        $this
            ->buildDatabasePopulator()
            ->populateData(
                $this->schemaConfigurator->requireLoadedModules(),
                $data
            );

        return $this;
    }

    public function begin(): self
    {
        $this->build();

        if ($this->connectionPopulate->isConnected() && !$this->connectionPopulate->isTransactionActive()) {
            $this->connectionPopulate->beginTransaction();
        }


        return $this;
    }

    public function rollback(): self
    {
        $this->build();

        if ($this->connectionPopulate->isConnected() && $this->connectionPopulate->isTransactionActive()) {
            $this->connectionPopulate->rollBack();
        }

        return $this;
    }

    public function commit(): self
    {
        $this->build();

        if ($this->connectionPopulate->isConnected() && $this->connectionPopulate->isTransactionActive()) {
            $this->connectionPopulate->commit();
        }


        return $this;
    }
}
