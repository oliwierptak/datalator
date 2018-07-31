<?php

declare(strict_types = 1);

namespace Tests\Datalator\Command;

use Datalator\Command\AbstractCommand;
use Datalator\Command\PopulateCommand;

class PopulateCommandTest extends AbstractCommandTest
{
    protected function getCommandUnderTest(): AbstractCommand
    {
        return new PopulateCommand();
    }

    protected function getCommandUnderTestName(): string
    {
        return PopulateCommand::COMMAND_NAME;
    }
}
