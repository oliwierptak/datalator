<?php

declare(strict_types = 1);

namespace Tests\Datalator;

use Datalator\DatalatorFacade;
use Datalator\Helper\TestDatabaseHelper;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ReaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class DatalatorFacadeTest extends TestCase
{
    const TEST_DATABASE_DATALATOR = 'test_database_datalator';

    const SCHEMA_PATH = \TESTS_FIXTURE_DIR . 'database/schema/';
    const DATA_PATH = \TESTS_FIXTURE_DIR . 'database/data/';

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
     * @var TestDatabaseHelper
     */
    protected static $databaseHelper;

    public static function setUpBeforeClass(): void
    {
        static::cleanState();
    }

    protected static function cleanState(): void
    {
        self::setupDatabaseHelper();

        static::$databaseHelper->dropDatabase(static::TEST_DATABASE_DATALATOR);
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

    public function testCreate(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $facade->create($configurator);

        $this->assertTrue(
            static::$databaseHelper->databaseExists(static::TEST_DATABASE_DATALATOR)
        );
    }

    public function testDrop(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $facade->create($configurator);

        $facade->drop($configurator);

        $this->assertFalse(
            static::$databaseHelper->databaseExists(static::TEST_DATABASE_DATALATOR)
        );
    }

    public function testPopulate(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $facade->populate($configurator);

        $value = $this->factory->createDatabaseReader($configurator)
            ->read($this->readerConfiguratorFooOne);

        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testPopulateFeatureOne(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $facade->populate($configurator);

        $value = $this->factory->createDatabaseReader($configurator)
            ->read($this->readerConfiguratorBuzz);

        $this->assertEquals('Buzz', $value->getDatabaseValue());
    }

    public function testImport(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $featureOneConfigurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $defaultFooConfigurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH)
            ->setModules([
                'foo',
            ]);

        $defaultBarConfigurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH)
            ->setModules([
                'bar',
            ]);

        $facade->import($featureOneConfigurator, [
            $defaultBarConfigurator,
            $defaultFooConfigurator,
        ]);

        $valueFooOne = $this->factory->createDatabaseReader($featureOneConfigurator)
            ->read($this->readerConfiguratorFooOne);

        $valueBuzz = $this->factory->createDatabaseReader($featureOneConfigurator)
            ->read($this->readerConfiguratorBuzz);

        $this->assertEquals('foo-one', $valueFooOne->getDatabaseValue());
        $this->assertEquals('Buzz', $valueBuzz->getDatabaseValue());
    }

    public function testReadFromDatabase(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $facade->populate($configurator);

        $value = $facade->readFromDatabase($configurator, $this->readerConfiguratorFooOne);

        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testReadDataWithoutIdentity(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $facade->populate($configurator);

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo_one')
            ->setIdentityValue('foo-one')
            ->setIdentityColumn('foo_one_key')
            ->setQueryColumn('foo_one_value');

        $value = $facade->readFromData($configurator, $readerConfigurator);

        $this->assertEquals('Foo One', $value->getDataValue());
    }

    public function testReadDataShouldReturnNullIfIdentityValueIsNotDefinedInDataSource(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath(static::SCHEMA_PATH)
            ->setDataPath(static::DATA_PATH);

        $facade->populate($configurator);

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo-one')
            ->setIdentityValue('INVALID')
            ->setQueryColumn('foo_one_key');

        $value = $facade->readFromData($configurator, $readerConfigurator);

        $this->assertNull($value->getDataValue());
    }
}
