<?php

declare(strict_types = 1);

namespace Tests\Datalator;

use Datalator\Popo\LoaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class PdoTests extends TestCase
{
    const TEST_DATABASE_DATALATOR = 'test_database_datalator';

    const SCHEMA_PATH = \TESTS_FIXTURE_DIR . 'database/schema/';
    const DATA_PATH = \TESTS_FIXTURE_DIR . 'database/data/';

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

        $this->dropDatabase();
    }

    public function dropDatabase(): void
    {
        if (!$this->databaseExists()) {
            return;
        }

        $c = $this->factory->createConnection($this->configurator, false);

        $c->getSchemaManager()->dropDatabase(
            static::TEST_DATABASE_DATALATOR
        );
    }

    public function databaseExists(): bool
    {
        $c = $this->factory->createConnection($this->configurator, false);

        try {
            return \in_array(
                static::TEST_DATABASE_DATALATOR,
                $c->getSchemaManager()->listDatabases()
            );
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function testCreate(): void
    {
        $pdo = $this->factory->createPdo($this->configurator, false);

        $sql = \sprintf('CREATE DATABASE `%s`', static::TEST_DATABASE_DATALATOR);
        $pdo->exec($sql);

        $this->assertTrue(
            $this->databaseExists()
        );
    }

    public function testPopulateAndRollback(): void
    {
        if ($this->databaseExists()) {
            $connection = $this->factory->createConnection($this->configurator, false);
            $connection->getSchemaManager()->dropDatabase(
                static::TEST_DATABASE_DATALATOR
            );
        }

        $connection = $this->factory->createConnection($this->configurator, false);
        $connection->getSchemaManager()->createDatabase(
            static::TEST_DATABASE_DATALATOR
        );

        $this->assertTrue(
            $this->databaseExists()
        );

        $pdo = $this->factory->createPdo($this->configurator, true);

        $sql = \sprintf('
CREATE TABLE `foo_table` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
');

        $pdo->exec($sql);


        $pdo->beginTransaction();
        $sql = "INSERT INTO `foo_table` (title) VALUES ('Lorem Ipsum')";
        $pdo->exec($sql);
        $pdo->rollBack();

        $sql = 'SELECT * FROM `foo_table`';
        $stm = $pdo->query($sql);
        $data = $stm->fetchAll();

        $this->assertEmpty($data);
    }

    public function testPopulateAndCommit(): void
    {
        if ($this->databaseExists()) {
            $connection = $this->factory->createConnection($this->configurator, false);
            $connection->getSchemaManager()->dropDatabase(
                static::TEST_DATABASE_DATALATOR
            );
        }

        $connection = $this->factory->createConnection($this->configurator, false);
        $connection->getSchemaManager()->createDatabase(
            static::TEST_DATABASE_DATALATOR
        );

        $this->assertTrue(
            $this->databaseExists()
        );

        $pdo = $this->factory->createPdo($this->configurator, true);

        $sql = \sprintf('
CREATE TABLE `foo_table` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
');

        $pdo->exec($sql);

        $pdo->beginTransaction();
        $sql = "INSERT INTO `foo_table` (title) VALUES ('Lorem Ipsum')";
        $pdo->exec($sql);

        $pdo->commit();

        $sql = 'SELECT * FROM `foo_table`';
        $stm = $pdo->query($sql);
        $data = $stm->fetchAll();

        $this->assertNotEmpty($data);
    }

}
