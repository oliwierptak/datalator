<?php

declare(strict_types = 1);

namespace Tests\Datalator\Schema;

use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\SchemaConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class SchemaLoaderTest extends TestCase
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

    public function testLoadDefault(): void
    {
        $schemaLoader = $this->factory->createSchemaLoader();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'foo',
                'bar',
                'invalidModuleThatDoesNotExist',
            ]);

        $schemaConfigurator = $schemaLoader->load($configurator);

        $this->assertInstanceOf(SchemaConfigurator::class, $schemaConfigurator);
        $this->assertEquals('default', $schemaConfigurator->requireSchemaName());

        $this->assertEquals(
            $schemaConfigurator->requireSchemaName(),
            $configurator->requireSchema()
        );

        $this->assertCount(2, $schemaConfigurator->getLoadedModules());
    }

    public function testLoadFoo(): void
    {
        $schemaLoader = $this->factory->createSchemaLoader();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default-feature-one')
            ->setData('default-feature-one')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'buzz',
            ]);

        $schemaConfigurator = $schemaLoader->load($configurator);

        $this->assertInstanceOf(SchemaConfigurator::class, $schemaConfigurator);
        $this->assertEquals('default-feature-one', $schemaConfigurator->getSchemaName());

        $this->assertEquals(
            $schemaConfigurator->getSchemaName(),
            $configurator->requireSchema()
        );

        $this->assertCount(1, $schemaConfigurator->getLoadedModules());
    }
}
