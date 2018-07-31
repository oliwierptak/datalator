<?php

declare(strict_types = 1);

namespace Datalator\Logger;

use Datalator\Logger\Handler\Monolog\FileLoggerHandler;
use Datalator\Logger\Handler\Monolog\MonologHandlerInterface;
use Datalator\Popo\LoggerChannel;
use Datalator\Popo\LoggerConfigurator;
use Monolog\Logger as MonologLogger;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArgvInput;

class LoggerFactory implements LoggerFactoryInterface
{
    const LOGGER_NAME = 'datalator';

    public function createLogger(LoggerConfigurator $configurator): LoggerInterface
    {
        $monologLogger = new MonologLogger($configurator->requireName());

        foreach ($this->createHandlerCollection($configurator) as $handler) {
            $handler->attach($monologLogger);
        }

        return $monologLogger;
    }
    /**
     * @param \Datalator\Popo\LoggerConfigurator $configurator
     *
     * @return \Datalator\Logger\Handler\Monolog\MonologHandlerInterface[]
     */
    public function createHandlerCollection(LoggerConfigurator $configurator): array
    {
        $handlers = [];
        foreach ($configurator->getChannels() as $channel) {
            $handlers[] = $this->createFileHandler($channel);
        }

        return $handlers;
    }

    public function createConfigurator(string $logFile): LoggerConfigurator
    {
        $items = $this->createChannelCollection($logFile);

        $configurator = (new LoggerConfigurator())
            ->setName(static::LOGGER_NAME)
            ->setChannels($items);

        return $configurator;
    }

    /**
     * @param string $logFile
     *
     * @return \Datalator\Popo\LoggerChannel[]
     */
    protected function createChannelCollection(string $logFile): array
    {
        $level = $this->detectVerbosity();
        $items = [];
        $configuration = [
            [Logger::ERROR, $logFile . '.error.log'],
            [Logger::ERROR, 'php://stdout'],
            [Logger::WARNING, 'php://stdout'],
            [Logger::INFO, 'php://stdout'],
            [Logger::DEBUG, 'php://stdout'],
        ];

        if ($level === 0) {
            $items[] = (new LoggerChannel())
                ->setLogLevel(Logger::ERROR)
                ->setLogFile($logFile . '.error.log');

            return $items;
        }

        foreach ($configuration as [$logLevel, $logFile]) {
            if ($level !== $logLevel) {
                continue;
            }

            $item = (new LoggerChannel())
                ->setLogLevel($logLevel)
                ->setLogFile($logFile);
            $items[] = $item;
        }

        return $items;
    }

    protected function createFileHandler(LoggerChannel $channel): MonologHandlerInterface
    {
        $handler = new FileLoggerHandler($channel);

        return $handler;
    }

    protected function detectVerbosity(): int
    {
        $input = new ArgvInput();

        try {
            if ($input->hasParameterOption('-q', true) ||
                $input->hasParameterOption('--quiet', true)) {
                return 0;
            }

            if ($input->hasParameterOption('-vvv', true) ||
                $input->hasParameterOption('--verbose=3', true) ||
                $input->getParameterOption('--verbose', false, true) === 3) {
                return Logger::DEBUG;
            }

            if ($input->hasParameterOption('-vv', true) ||
                $input->hasParameterOption('--verbose=2', true) ||
                $input->getParameterOption('--verbose', false, true) === 2) {
                return Logger::INFO;
            }

            if ($input->hasParameterOption('-v', true) ||
                $input->hasParameterOption('--verbose=1', true) ||
                $input->hasParameterOption('--verbose', true) ||
                $input->getParameterOption('--verbose', false, true)) {
                return Logger::WARNING;
            }
        } catch (\Throwable $t) {
        }

        return Logger::ERROR;
    }
}
