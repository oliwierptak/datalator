<?php

declare(strict_types = 1);

namespace Tests\Datalator\Helper;

use Datalator\Helper\TestPopulator;
use Datalator\Popo\ReaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class DatalatorHelperTest extends TestCase
{
    const TEST_DATABASE_DATALATOR = 'test_database_datalator';
    const TEST_DATABASE_DATALATOR_FEATURE_ONE = 'test_database_datalator_feature_one';

    /**
     * @var string
     */
    protected $schemaPath;

    /**
     * @var string
     */
    protected $dataPath;

    /**
     * @var \Datalator\Helper\TestPopulator
     */
    protected $testPopulator;

    /**
     * @var \Datalator\Popo\ReaderConfigurator
     */
    protected $readerConfiguratorFooOne;

    /**
     * @var \Datalator\Popo\ReaderConfigurator
     */
    protected $readerConfiguratorBar;

    /**
     * @var \Datalator\Popo\ReaderConfigurator
     */
    protected $readerConfiguratorBuzz;

    /**
     * @var \Datalator\DatalatorFactoryInterface
     */
    protected $factory;

    /**
     * @var \Datalator\Builder\DatabaseBuilderInterface
     */
    protected $databaseBuilder;

    protected function setUp(): void
    {
        $this->schemaPath = \TESTS_FIXTURE_DIR. 'database/schema/';
        $this->dataPath = \TESTS_FIXTURE_DIR . 'database/data/';

        $this->testPopulator = (new TestPopulator())
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath);

        $this->readerConfiguratorFooOne = (new ReaderConfigurator())
            ->setSource('foo_one')
            ->setIdentityValue(1)
            ->setQueryColumn('foo_one_key');

        $this->readerConfiguratorBar = (new ReaderConfigurator())
            ->setSource('bar')
            ->setIdentityValue(1)
            ->setQueryColumn('value');

        $this->readerConfiguratorBuzz = (new ReaderConfigurator())
            ->setSource('buzz')
            ->setIdentityValue(1)
            ->setQueryColumn('value');

        $this->factory = new DatalatorFactoryStub();

        $this->databaseBuilder = $this->factory->createDatabaseBuilder(
            $this->testPopulator->dumpConfiguratorInstance()
        );
    }

    public function testCreate(): void
    {
        $this->testPopulator->create();

        $this->assertTrue(
            $this->databaseBuilder->databaseExists(static::TEST_DATABASE_DATALATOR)
        );
    }

    public function testDrop(): void
    {
        $this->testPopulator->drop();

        $this->assertFalse(
            $this->databaseBuilder->databaseExists(static::TEST_DATABASE_DATALATOR)
        );
    }

    public function testPopulate(): void
    {
        $this->testPopulator->populate();

        $value = $this->factory->createDatabaseReader($this->testPopulator->dumpConfiguratorInstance())
            ->read($this->readerConfiguratorFooOne);

        $this->assertEquals('foo-one', $value->getDatabaseValue());
    }

    public function testUseCreate(): void
    {
        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->create();

        $this->assertTrue(
            $this->databaseBuilder->databaseExists(static::TEST_DATABASE_DATALATOR_FEATURE_ONE)
        );
    }

    public function testUseDrop(): void
    {
        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->drop();

        $this->assertFalse(
            $this->databaseBuilder->databaseExists(static::TEST_DATABASE_DATALATOR_FEATURE_ONE)
        );
    }

    public function testUsePopulate(): void
    {
        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->populate();

        $value = $this->factory->createDatabaseReader($this->testPopulator->dumpConfiguratorInstance())
            ->read($this->readerConfiguratorBuzz);

        $this->assertEquals('Buzz', $value->getDatabaseValue());
    }

    public function testUseImportFrom(): void
    {
        $defaultConfiguratorBar = $this->testPopulator->dumpConfiguratorInstance();
        $defaultConfiguratorBar->setModules(['bar']);

        $defaultConfiguratorFoo = $this->testPopulator->dumpConfiguratorInstance();
        $defaultConfiguratorFoo->setModules(['foo']);

        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->useModules(['buzz'])
            ->importFrom(
                [$defaultConfiguratorBar, $defaultConfiguratorFoo]
            );

        $valueFoo = $this->factory->createDatabaseReader($defaultConfiguratorFoo)
            ->read($this->readerConfiguratorFooOne);

        $valueBar = $this->factory->createDatabaseReader($defaultConfiguratorBar)
            ->read($this->readerConfiguratorBar);

        $valueBuzz = $this->factory->createDatabaseReader($this->testPopulator->dumpConfiguratorInstance())
            ->read($this->readerConfiguratorBuzz);

        $this->assertEquals('foo-one', $valueFoo->getDatabaseValue());
        $this->assertEquals('Bar', $valueBar->getDatabaseValue());
        $this->assertEquals('Buzz', $valueBuzz->getDatabaseValue());
    }

    public function testUseExtend(): void
    {
        $defaultConfigurator = $this->testPopulator->dumpConfiguratorInstance();
        $defaultConfigurator->setModules(['bar']);

        $defaultConfiguratorFoo = $this->testPopulator->dumpConfiguratorInstance();
        $defaultConfiguratorFoo->setModules(['foo']);

        $this->testPopulator
            ->useSchemaName('default-feature-one')
            ->useDataName('default-feature-one')
            ->useSchemaPath($this->schemaPath)
            ->useDataPath($this->dataPath)
            ->extendWith(
                $defaultConfigurator
            );

        $valueFoo = $this->factory->createDatabaseReader($defaultConfiguratorFoo)
            ->read($this->readerConfiguratorFooOne);

        $valueBar = $this->factory->createDatabaseReader($defaultConfigurator)
            ->read($this->readerConfiguratorBar);

        $this->assertEquals('foo-one', $valueFoo->getDatabaseValue());
        $this->assertEquals('Bar', $valueBar->getDatabaseValue());
    }
}
