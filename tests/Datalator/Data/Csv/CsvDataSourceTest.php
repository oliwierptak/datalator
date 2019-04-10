<?php

declare(strict_types = 1);

namespace Tests\Datalator\Data\Csv;

use Datalator\Data\Csv\CsvDataSource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Tests\DatalatorStub\Datalator\DatalatorFactoryStub;

class CsvDataSourceTest extends TestCase
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

    /**
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    protected $csvFile;

    protected function setUp(): void
    {
        $this->schemaDir = \TESTS_FIXTURE_DIR. 'database/schema/';
        $this->dataDir = \TESTS_FIXTURE_DIR . 'database/data/';

        $this->csvFile = new SplFileInfo(
            $this->dataDir . 'default-newlines/buzz/buzz.csv',
            $this->dataDir . 'default-newlines/buzz/',
            'buzz.csv'
        );

        $this->factory = new DatalatorFactoryStub();
    }

    public function testGetDataShouldHandleNewLinesInValues(): void
    {
        $csvData = new CsvDataSource($this->csvFile);
        $data = $csvData->getData();
        $this->assertNotEmpty($data);

        $this->assertCount(4, $data);
        $this->assertCount(3, $data[3]);
        $this->assertEquals(3, $data[2]['id']);
        $this->assertSame("Lorem Ipsum
with
new
lines", $data[2]['value']);
    }

    public function testiIsLoadedShouldReturnFalse(): void
    {
        $csvData = new CsvDataSource($this->csvFile);
        $this->assertFalse($csvData->isLoaded());
    }

    public function testIsLoadedShouldReturnTrue(): void
    {
        $csvData = new CsvDataSource($this->csvFile);

        $csvData->getData();

        $this->assertTrue($csvData->isLoaded());
    }

    public function testGetColumns(): void
    {
        $csvData = new CsvDataSource($this->csvFile);

        $columns = $csvData->getColumns();

        $this->assertEquals(['id','value','datetime'], $columns);
    }

    public function testGetName(): void
    {
        $csvData = new CsvDataSource($this->csvFile);

        $name = $csvData->getName();

        $this->assertEquals('buzz', $name);
    }
}
