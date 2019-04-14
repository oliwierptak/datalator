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
        $this->configure();

        if ($this->schemaLoader === null) {
            $this->schemaLoader = $this->getFactory()->createSchemaLoader();
        }

        return $this;
    }

    public function databaseExists(string $databaseName): bool
    {
        $this->configure();
        $connection = $this->getFactory()->createConnection($this->configurator, false);

        return \in_array(
            $databaseName,
            $connection->getSchemaManager()->listDatabases()
        );
    }

    public function createDatabase(string $databaseName): void
    {
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
        if (!$this->databaseExists($databaseName)) {
            return;
        }

        $connection = $this->getFactory()->createConnection($this->configurator, false);

        $connection->getSchemaManager()->dropDatabase(
            $databaseName
        );
    }
}
