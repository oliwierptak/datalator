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

        $fp = \fopen($this->csvFile->getPathname(), 'r');
        $data = [];
        while ($row = \fgetcsv($fp)) {
            $data[] = $row;
        }
        \fclose($fp);

        $this->columns = \array_shift($data);
        $this->data = \array_map([$this, 'prepareRow'], $data);

        $this->loadedTimestamp = \time();
    }

    /**
     * @param array $data
     *
     * @return array
     * @throws \UnexpectedValueException
     *
     */
    protected function prepareRow(array $data): array
    {
        $dataCount = \count($data);
        $columnCount = \count($this->columns);

        if ($dataCount !== $columnCount) {
            throw new \UnexpectedValueException(sprintf(
                'Data count (%d) and column count (%d) do not match in "%s".',
                $dataCount,
                $columnCount,
                $this->csvFile->getPathname()
            ));
        }

        $row = \array_combine(
            $this->columns,
            $data
        );

        return $row;
    }
}
