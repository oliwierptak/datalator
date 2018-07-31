<?php

declare(strict_types = 1);

namespace Datalator\Logger\Handler\Monolog;

use Datalator\Popo\LoggerChannel;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class FileLoggerHandler implements MonologHandlerInterface
{
    /**
     * @var \Datalator\Popo\LoggerChannel
     */
    protected $channel;

    public function __construct(LoggerChannel $channel)
    {
        $this->channel = $channel;
    }

    public function attach(Logger $logger): void
    {
        $logger->pushHandler(
            $this->build()
        );
    }

    protected function build(): HandlerInterface
    {
        $handler = new StreamHandler(
            $this->channel->requireLogFile(),
            $this->channel->requireLogLevel()
        );

        $handler->setFormatter(
            $this->createMonologLineFormatter()
        );

        return $handler;
    }

    protected function createMonologLineFormatter(): LineFormatter
    {
        $output = "[%datetime%] %channel%.%level_name%: %message%\n";
        //$output = "%datetime% %level_name%: %message%\n";

        return new LineFormatter($output);
    }
}
