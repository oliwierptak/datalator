<?php

declare(strict_types = 1);

namespace Datalator\Loader;

use Datalator\Popo\LoaderConfigurator;

interface DataLoaderInterface
{
    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @return \Datalator\Data\DataSourceInterface[]
     */
    public function load(LoaderConfigurator $configurator): array;
}
