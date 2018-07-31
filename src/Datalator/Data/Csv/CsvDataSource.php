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
        $data = $this->data;

        \array_shift($data);

        return $data;
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

    public function getColumns(): array
    {
        $this->load();
        $data = $this->data;

        return \array_shift($data);
    }

    protected function load(): void
    {
        if ($this->loadedTimestamp !== null) {
            return;
        }

        $this->data = \array_map('str_getcsv', \file($this->csvFile->getPathname()));
        $this->loadedTimestamp = \time();
    }
}
