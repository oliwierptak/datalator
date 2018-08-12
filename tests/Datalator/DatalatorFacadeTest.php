<?php

declare(strict_types = 1);

namespace Tests\Datalator;

use Datalator\DatalatorFacade;
use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ReaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class DatalatorFacadeTest extends TestCase
{
    /**
     * @var string
     */
    protected $schemaDir;

    /**
     * @var string
     */
    protected $dataDir;

    /**
     * @var \Datalator\DatalatorFactoryInterface
     */
    protected $factory;

    protected function setUp(): void
    {
        $this->schemaDir = \TESTS_FIXTURE_DIR. 'database/schema/';
        $this->dataDir = \TESTS_FIXTURE_DIR . 'database/data/';

        $this->factory = new DatalatorFactoryStub();
    }

    public function testCreate(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->create($configurator);
    }

    public function testDrop(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->create($configurator);

        $facade->drop($configurator);
    }

    public function testPopulate(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);
    }

    public function testPopulateFeatureOne(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);
    }

    public function testImport(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $featureOneConfigurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $defaultFooConfigurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'foo',
            ]);

        $defaultBarConfigurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'bar',
            ]);

        $facade->import($featureOneConfigurator, [
            $defaultBarConfigurator,
            $defaultFooConfigurator,
        ]);
    }

    public function testReadSchema(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo_one')
            ->setIdentityValue(1)
            ->setQueryColumn('foo_one_key');

        $value = $facade->readFromSchema($configurator, $readerConfigurator);

        $this->assertEquals('foo-one', $value->getSchemaValue());
    }

    public function testReadDataWithoutIdentity(): void
    {
        $facade = new DatalatorFacade();
        $facade->setFactory($this->factory);

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

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
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir);

        $facade->populate($configurator);

        $readerConfigurator = (new ReaderConfigurator())
            ->setSource('foo-one')
            ->setIdentityValue('INVALID')
            ->setQueryColumn('foo_one_key');

        $value = $facade->readFromData($configurator, $readerConfigurator);

        $this->assertNull($value->getDataValue());
    }
}
