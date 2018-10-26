<?php

declare(strict_types = 1);

namespace Datalator\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends AbstractCommand
{
    const COMMAND_NAME = 'create';
    const COMMAND_DESCRIPTION = 'Create the test database. The database will dropped if it exists.';

    protected function executeCommand(InputInterface $input, OutputInterface $output): ?int
    {
        $configurator = $this->buildConfigurator($input);
        $this->getFacade()->create($configurator);

        return 0;
    }
}
