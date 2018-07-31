<?php

declare(strict_types = 1);

namespace Tests\Datalator\Command;

use Datalator\Command\AbstractCommand;
use Datalator\Command\DropCommand;

class DropCommandTest extends AbstractCommandTest
{
    protected function getCommandUnderTest(): AbstractCommand
    {
        return new DropCommand();
    }

    protected function getCommandUnderTestName(): string
    {
        return DropCommand::COMMAND_NAME;
    }
}
