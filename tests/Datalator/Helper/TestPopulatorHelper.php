<?php

declare(strict_types = 1);

namespace Tests\Datalator\Helper;

use Datalator\Helper\TestDatabaseHelper;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ReaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class TestPopulatorHelper extends TestCase
{
    const TEST_DATABASE_DATALATOR = 'test_database_datalator';
    const TEST_DATABASE_DATALATOR_FEATURE_ONE = 'test_database_datalator_feature_one';

    const SCHEMA_PATH = \TESTS_FIXTURE_DIR . 'database/schema/';
    const DATA_PATH = \TESTS_FIXTURE_DIR . 'database/data/';

    /**
     * @var TestDatabaseHelper
     */
    protected static $databaseHelper;

    /**
     * @var \Datalator\DatalatorFactoryInterface
     */
    protected $factory;

    /**
     * @var \Datalator\Popo\ReaderConfigurator
     */
    protected $readerConfiguratorFooOne;

    /**
     * @var \Datalator\Popo\ReaderConfigurator
     */
    protected $readerConfiguratorBuzz;

    /**
     * @var \Datalator\Helper\TestPopulatorHelper
     */
    protected $databasePopulator;

    public static function setUpBeforeClass()
    {
        static::cleanState();
    }

    protected static function cleanState(): void
    {
        self::setupDatabaseHelper();

        static::$databaseHelper->dropDatabase(static::TEST_DATABASE_DATALATOR);
        static::$databaseHelper->dropDatabase(static::TEST_DATABASE_DATALATOR_FEATURE_ONE);
    }

    protected static function setupDatabaseHelper(): void
    {
        if (static::$databaseHelper === null) {
            $configurator = (new LoaderConfigurator())
                ->setSchema('default')
                ->setData('default')
                ->setSchemaPath(static::SCHEMA_PATH)
                ->setDataPath(static::DATA_PATH);

            static::$databaseHelper = new TestDatabaseHelper();
            static::$databaseHelper->setFactory(new DatalatorFactoryStub());
            static::$databaseHelper->setConfigurator($configurator);
        }
    }

    protected function setUp(): void
    {
        $this->readerConfiguratorFooOne = (new ReaderConfigurator())
            ->setSource('foo_one')
            ->setIdentityValue(1)
            ->setQueryColumn('foo_one_key');

        $this->readerConfiguratorBuzz = (new ReaderConfigurator())
            ->setSource('buzz')
            ->setIdentityValue(1)
            ->setQueryColumn('value');

        $this->factory = new DatalatorFactoryStub();
    }

    protected function tearDown()
    {
        $this->databasePopulator->rollback();
    }

    public function testPopulate(): void
    {
        $this->databasePopulator = new TestPopulatorHelper();
        $this->databasePopulator->setFactory($this->factory);

        $this->databasePopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testPopulateFeatureOne(): void
    {
        $this->databasePopulator = new TestPopulatorHelper();
        $this->databasePopulator->setFactory($this->factory);

        $this->databasePopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorBuzz);
        $this->assertEquals('Buzz', $value->getDatabaseValue());
    }

    public function testReadFromDatabase(): void
    {
        $this->databasePopulator = new TestPopulatorHelper();
        $this->databasePopulator->setFactory($this->factory);

        $this->databasePopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testReadDataByIdentityValue(): void
    {
        $this->databasePopulator = new TestPopulatorHelper();
        $this->databasePopulator->setFactory($this->factory);

        $this->databasePopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo_one')
            ->setIdentityValue('foo-one')
            ->setIdentityColumn('foo_one_key')
            ->setQueryColumn('foo_one_value');

        $value = $this->databasePopulator->readValue($readerConfigurator);

        $this->assertEquals('Foo One', $value->getDatabaseValue());
    }

    public function testReadDataShouldReturnNullIfIdentityValueIsNotDefinedInDataSource(): void
    {
        $this->databasePopulator = new TestPopulatorHelper();
        $this->databasePopulator->setFactory($this->factory);

        $this->databasePopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo-one')
            ->setIdentityValue('INVALID')
            ->setQueryColumn('foo_one_key');

        $value = $this->databasePopulator->readValue($readerConfigurator);

        $this->assertNull($value->getDatabaseValue());
    }

    public function testPopulateWithRollback(): void
    {
        $this->databasePopulator = new TestPopulatorHelper();
        $this->databasePopulator->setFactory($this->factory);

        $this->databasePopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());

        $this->databasePopulator->rollback();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertNull($value->getDatabaseValue());
    }

    public function testPopulateWithCommit(): void
    {
        $this->databasePopulator = new TestPopulatorHelper();
        $this->databasePopulator->setFactory($this->factory);

        $this->databasePopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());

        $this->databasePopulator->commit();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testPopulateWithCommitFeatureOne(): void
    {
        $this->databasePopulator = new TestPopulatorHelper();
        $this->databasePopulator->setFactory($this->factory);

        $this->databasePopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorBuzz);
        $this->assertEquals('Buzz', $value->getDatabaseValue());

        $this->databasePopulator->commit();

        $value = $this->databasePopulator->readValue($this->readerConfiguratorBuzz);
        $this->assertEquals('Buzz', $value->getDatabaseValue());
    }
}
