<?php

declare(strict_types = 1);

namespace Datalator\Helper;

class DatabaseHelper
{
    use TraitHelper;

    /**
     * @var \Datalator\Loader\Schema\SchemaLoaderInterface
     */
    protected $schemaLoader;

    protected function build(): self
    {
        if ($this->isDone) {
            return $this;
        }

        $this->configure();
        $this->schemaLoader = $this->getFactory()->createSchemaLoader();
        $this->done();

        return $this;
    }

    public function databaseExists(string $databaseName): bool
    {
        $this->build();

        $connection = $this->getFactory()->createConnection($this->configurator, false);

        return \in_array(
            $databaseName,
            $connection->getSchemaManager()->listDatabases()
        );
    }

    public function createDatabase(string $databaseName): void
    {
        $this->build();

        if ($this->databaseExists($databaseName)) {
            $this->dropDatabase($databaseName);
        }

        $connection = $this->getFactory()->createConnection($this->configurator, false);

        $connection->getSchemaManager()->createDatabase(
            $databaseName
        );
    }

    public function dropDatabase(string $databaseName): void
    {
        $this->build();

        if (!$this->databaseExists($databaseName)) {
            return;
        }

        $connection = $this->getFactory()->createConnection($this->configurator, false);

        $connection->getSchemaManager()->dropDatabase(
            $databaseName
        );
    }
}
