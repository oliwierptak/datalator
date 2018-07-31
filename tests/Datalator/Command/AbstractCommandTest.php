<?php

declare(strict_types = 1);

namespace Tests\Datalator\Command;

use Datalator\Command\AbstractCommand;
use Datalator\Command\CreateCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\DatalatorStub\Datalator\DatalatorFacadeStub;

abstract class AbstractCommandTest extends TestCase
{
    /**
     * @var array
     */
    protected $defaultArguments = [
        '--' . CreateCommand::OPTION_SCHEMA => 'default',
        '--' . CreateCommand::OPTION_DATA => 'default',
        '--' . CreateCommand::OPTION_SCHEMA_PATH => 'schema/',
        '--' . CreateCommand::OPTION_DATA_PATH => 'data/',
        '--' . CreateCommand::OPTION_MODULES => [],
        '--' . CreateCommand::OPTION_PATH => \TESTS_FIXTURE_DIR . 'database/',
    ];

    abstract protected function getCommandUnderTest(): AbstractCommand;

    abstract protected function getCommandUnderTestName(): string;

    public function testExecute(): void
    {
        $commandTester = $this->getCommandTester();

        $result = $commandTester->execute($this->defaultArguments);

        $this->assertEquals(0, $result);
    }

    public function testExecuteDefaults(): void
    {
        $commandTester = $this->getCommandTester();

        $result = $commandTester->execute(
            $this->defaultArguments
        );

        $this->assertEquals(0, $result);
    }

    protected function getCommandTester(): CommandTester
    {
        $commandUnderTest = $this->getCommandUnderTest();

        $application = new Application();
        $application->add($commandUnderTest);

        /** @var \Datalator\Command\AbstractCommand $command */
        $command = $application->find($this->getCommandUnderTestName());
        $command->setFacade(new DatalatorFacadeStub());

        $commandTester = new CommandTester($command);

        return $commandTester;
    }
}
