<?php

declare(strict_types = 1);

namespace Datalator\Reader;

use Datalator\Popo\ReaderConfigurator;
use Datalator\Popo\ReaderValue;

interface ReaderInterface
{
    public function read(ReaderConfigurator $configurator): ReaderValue;
}
