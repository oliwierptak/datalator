<?php

declare(strict_types = 1);

namespace Tests\Datalator\Helper;

use Datalator\Helper\TestDatabaseHelper;
use Datalator\Helper\TestDatabasePopulator;
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
    protected static $testDatabaseHelper;

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
     * @var \Datalator\Reader\ReaderInterface
     */
    protected $defaultDatabaseReader;

    /**
     * @var \Datalator\Reader\ReaderInterface
     */
    protected $featureOneDatabaseReader;

    /**
     * @var \Datalator\Helper\TestDatabasePopulator
     */
    protected $testPopulator;

    public static function setUpBeforeClass()
    {
        static::cleanState();
    }

    protected static function cleanState(): void
    {
        self::setupDatabaseHelper();

        static::$testDatabaseHelper->dropDatabase(static::TEST_DATABASE_DATALATOR);
        static::$testDatabaseHelper->dropDatabase(static::TEST_DATABASE_DATALATOR_FEATURE_ONE);
    }

    protected static function setupDatabaseHelper(): void
    {
        if (static::$testDatabaseHelper === null) {
            $configurator = (new LoaderConfigurator())
                ->setSchema('default')
                ->setData('default')
                ->setSchemaPath(static::SCHEMA_PATH)
                ->setDataPath(static::DATA_PATH);

            static::$testDatabaseHelper = new TestDatabaseHelper();
            static::$testDatabaseHelper->setFactory(new DatalatorFactoryStub());
            static::$testDatabaseHelper->setConfigurator($configurator);
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

        $defaultReaderConfigurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $featureOneReaderConfigurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $this->factory = new DatalatorFactoryStub();

        $this->defaultDatabaseReader = $this->factory->createDatabaseReader($defaultReaderConfigurator);
        $this->featureOneDatabaseReader = $this->factory->createDatabaseReader($featureOneReaderConfigurator);

        $this->testPopulator = new TestDatabasePopulator();
        $this->testPopulator->setFactory($this->factory);
    }

    protected function tearDown()
    {
        $this->testPopulator->rollback();
    }

    public function testPopulate(): void
    {
        $this->testPopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->testPopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testPopulateFeatureOne(): void
    {
        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->testPopulator->readValue($this->readerConfiguratorBuzz);
        $this->assertEquals('Buzz', $value->getDatabaseValue());
    }

    public function testReadFromDatabase(): void
    {
        $this->testPopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->testPopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testReadDataByIdentityValue(): void
    {
        $this->testPopulator
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

        $value = $this->testPopulator->readValue($readerConfigurator);

        $this->assertEquals('Foo One', $value->getDatabaseValue());
    }

    public function testReadDataShouldReturnNullIfIdentityValueIsNotDefinedInDataSource(): void
    {
        $this->testPopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo-one')
            ->setIdentityValue('INVALID')
            ->setQueryColumn('foo_one_key');

        $value = $this->testPopulator->readValue($readerConfigurator);

        $this->assertNull($value->getDatabaseValue());
    }

    public function testPopulateWithRollback(): void
    {
        $this->testPopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->testPopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());

        $this->testPopulator->rollback();

        $value = $this->testPopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertNull($value->getDatabaseValue());
    }

    public function testPopulateWithCommit(): void
    {
        $this->testPopulator
            ->useSchemaName('default')
            ->useDataName('default')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->testPopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());

        $this->testPopulator->commit();

        $value = $this->testPopulator->readValue($this->readerConfiguratorFooOne);
        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testPopulateWithCommitFeatureOne(): void
    {
        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath(static::SCHEMA_PATH)
            ->useDataPath(static::DATA_PATH)
            ->populate();

        $value = $this->testPopulator->readValue($this->readerConfiguratorBuzz);
        $this->assertEquals('Buzz', $value->getDatabaseValue());

        $this->testPopulator->commit();

        $value = $this->testPopulator->readValue($this->readerConfiguratorBuzz);
        $this->assertEquals('Buzz', $value->getDatabaseValue());
    }
}
