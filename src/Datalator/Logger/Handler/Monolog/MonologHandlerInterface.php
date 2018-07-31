<?php

declare(strict_types = 1);

namespace Datalator\Logger\Handler\Monolog;

use Monolog\Logger;

interface MonologHandlerInterface
{
    public function attach(Logger $logger): void;
}
