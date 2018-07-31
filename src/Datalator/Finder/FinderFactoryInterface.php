<?php

declare(strict_types = 1);

namespace Datalator\Finder;

use Symfony\Component\Finder\Finder;

interface FinderFactoryInterface
{
    public function createFileLoader(): FileLoaderInterface;

    public function createFinder(): Finder;
}
