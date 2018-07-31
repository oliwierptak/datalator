<?php

declare(strict_types = 1);

namespace Datalator\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateCommand extends AbstractCommand
{
    const COMMAND_NAME = 'populate';
    const COMMAND_DESCRIPTION = 'Populate the test database. The database will created / dropped if needed.';

    protected function executeCommand(InputInterface $input, OutputInterface $output): ?int
    {
        $configurator = $this->buildConfigurator($input);
        $this->getFacade()->populate($configurator);

        return 0;
    }
}
