<?php

declare(strict_types = 1);

namespace Datalator\Data\Csv;

use Datalator\Data\DataSourceInterface;
use Datalator\Loader\AbstractDataLoader;
use Symfony\Component\Finder\SplFileInfo;

class CsvDataLoader extends AbstractDataLoader
{
    const TYPE = 'csv';

    protected function buildDataItem(SplFileInfo $file): DataSourceInterface
    {
        return new CsvDataSource($file);
    }
}
