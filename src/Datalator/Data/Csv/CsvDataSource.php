<?php

declare(strict_types = 1);

namespace Datalator\Data\Csv;

use Datalator\Data\DataSourceInterface;
use Symfony\Component\Finder\SplFileInfo;

class CsvDataSource implements DataSourceInterface
{
    /**
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    protected $csvFile;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $columns;

    /**
     * @var int
     */
    protected $loadedTimestamp;

    public function __construct(SplFileInfo $csvFile)
    {
        $this->csvFile = $csvFile;
    }

    public function getData(): array
    {
        $this->load();

        return $this->data;
    }

    public function getColumns(): array
    {
        $this->load();

        return $this->columns;
    }

    public function isLoaded(): bool
    {
        return $this->loadedTimestamp !== null;
    }

    public function getName(): string
    {
        if ($this->name === null) {
            $this->name = \basename($this->csvFile->getPathname(), '.' . $this->csvFile->getExtension());
        }

        return $this->name;
    }

    protected function load(): void
    {
        if ($this->loadedTimestamp !== null) {
            return;
        }

        $handle = \fopen($this->csvFile->getPathname(), 'r');
        while ($row = \fgetcsv($handle)) {
            if ($this->columns === null) {
                $this->columns = $row;
                continue;
            }

            $this->data[] = $this->prepareRow($row);
        }

        $this->loadedTimestamp = \time();
    }

    /**
     * @param array $data
     *
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    protected function prepareRow(array $data): array
    {
        $dataCount = \count($data);
        $columnCount = \count($this->columns);

        if ($dataCount !== $columnCount) {
            throw new \UnexpectedValueException('Data and column count does not match');
        }

        $row = \array_combine(
            $this->columns,
            $data
        );

        return $row;
    }
}
