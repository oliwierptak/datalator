<?php

declare(strict_types = 1);

namespace Tests\Datalator;

use Datalator\Popo\LoaderConfigurator;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class ConnectionTest extends TestCase
{
    const TEST_DATABASE_DATALATOR = 'test_database_datalator';

    const SCHEMA_PATH = \TESTS_FIXTURE_DIR . 'database/schema/';
    const DATA_PATH = \TESTS_FIXTURE_DIR . 'database/data/';

    const SQL_TABLE = 'CREATE TABLE `foo_table` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;';

    /**
     * @var \Datalator\DatalatorFactoryInterface
     */
    protected $factory;

    /**
     * @var \Datalator\Popo\LoaderConfigurator
     */
    protected $configurator;

    protected function setUp(): void
    {
        $this->configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $this->factory = new DatalatorFactoryStub();

        $connection = $this->factory->createConnection($this->configurator, false);
        $this->dropTestDatabase($connection);
        $this->createTestDatabase($connection);
    }

    public function createTestDatabase(Connection $connection)
    {
        if (!$this->databaseTestExists($connection)) {
            $connection->getSchemaManager()->createDatabase(static::TEST_DATABASE_DATALATOR);
        }
    }

    public function dropTestDatabase(Connection $connection): void
    {
        if (!$this->databaseTestExists($connection)) {
            return;
        }

        $connection->getSchemaManager()->dropDatabase(
            static::TEST_DATABASE_DATALATOR
        );
    }

    public function databaseTestExists(Connection $connection): bool
    {
        try {
            return \in_array(
                static::TEST_DATABASE_DATALATOR,
                $connection->getSchemaManager()->listDatabases()
            );
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function testPopulateAndRollback(): void
    {
        $connection = $this->factory->createConnection($this->configurator, true);
        $connection->executeQuery(static::SQL_TABLE);

        $connection->beginTransaction();

        $connection->insert(
            'foo_table',
            ['title' => 'Lorem Ipsum' ]
        );

        $connection->rollBack();

        $stm = $connection->executeQuery('SELECT * FROM `foo_table`');
        $data = $stm->fetchAll();
        $this->assertEmpty($data);
    }

    public function testPopulateAndCommit(): void
    {
        $connection = $this->factory->createConnection($this->configurator, true);

        $connection->beginTransaction();
        $connection->executeQuery(static::SQL_TABLE);


        $connection->insert(
            'foo_table',
            ['title' => 'Lorem Ipsum' ]
        );

        $connection->commit();

        $stm = $connection->executeQuery('SELECT * FROM `foo_table`');
        $data = $stm->fetchAll();
        $this->assertNotEmpty($data);
    }

}
