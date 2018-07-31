<?php

declare(strict_types = 1);

namespace Tests\Datalator\Command;

use Datalator\Command\AbstractCommand;
use Datalator\Command\CreateCommand;

class CreateCommandTest extends AbstractCommandTest
{
    protected function getCommandUnderTest(): AbstractCommand
    {
        return new CreateCommand();
    }

    protected function getCommandUnderTestName(): string
    {
        return CreateCommand::COMMAND_NAME;
    }
}
