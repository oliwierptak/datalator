<?php

declare(strict_types = 1);

namespace Datalator\Logger;

use Datalator\Popo\LoggerConfigurator;
use Psr\Log\LoggerInterface;

interface LoggerFactoryInterface
{
    public function createLogger(LoggerConfigurator $configurator): LoggerInterface;

    /**
     * @param \Datalator\Popo\LoggerConfigurator $configurator
     *
     * @return \Datalator\Logger\Handler\Monolog\MonologHandlerInterface[]
     */
    public function createHandlerCollection(LoggerConfigurator $configurator): array;

    public function createConfigurator(string $logFile): LoggerConfigurator;
}
