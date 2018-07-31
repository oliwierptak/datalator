<?php

declare(strict_types = 1);

namespace Datalator\Loader;

use Datalator\Popo\LoaderConfigurator;

interface DataLoaderInterface
{
    public function load(LoaderConfigurator $configurator): array;
}
