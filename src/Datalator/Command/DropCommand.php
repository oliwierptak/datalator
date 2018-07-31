<?php

declare(strict_types = 1);

namespace Datalator\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DropCommand extends AbstractCommand
{
    const COMMAND_NAME = 'drop';
    const COMMAND_DESCRIPTION = 'Drop the test database if it exists.';

    protected function executeCommand(InputInterface $input, OutputInterface $output): ?int
    {
        $configurator = $this->buildConfigurator($input);
        $this->getFacade()->drop($configurator);

        return 0;
    }
}
