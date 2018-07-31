<?php

declare(strict_types = 1);

namespace Tests\Datalator\Data\Csv;

use Datalator\Popo\LoaderConfigurator;
use PHPUnit\Framework\TestCase;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class CsvDataLoaderTest extends TestCase
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

    public function testLoadData(): void
    {
        $csvDataLoader = $this->factory->createCsvDataLoader();

        $configurator = (new LoaderConfigurator())
            ->setSchema('default')
            ->setData('default')
            ->setSchemaPath($this->schemaDir)
            ->setDataPath($this->dataDir)
            ->setModules([
                'foo',
                'invalidModuleThatDoesNotExist',
            ]);

        $dataCollection = $csvDataLoader->load($configurator);

        $this->assertArrayHasKey('foo', $dataCollection);

        foreach ($dataCollection as $moduleName => $dataLoaderCollection) {
            $this->assertNotEmpty($dataLoaderCollection);

            foreach ($dataLoaderCollection as $csvData) {
                /** @var \Datalator\Data\DataSourceInterface $csvData */
                $data = $csvData->getData();
                $this->assertNotEmpty($data);
            }
        }
    }
}
