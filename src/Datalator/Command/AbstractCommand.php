<?php

declare(strict_types = 1);

namespace Datalator\Command;

use Datalator\DatalatorFacade;
use Datalator\DatalatorFacadeInterface;
use Datalator\Popo\LoaderConfigurator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    const COMMAND_NAME = 'unknown';
    const COMMAND_DESCRIPTION = 'unknown';

    const OPTION_SCHEMA = 'schema';
    const OPTION_DATA = 'data';
    const OPTION_SCHEMA_PATH = 'schemaPath';
    const OPTION_DATA_PATH = 'dataPath';
    const OPTION_MODULES = 'modules';
    const OPTION_PATH = 'path';

    /**
     * @var \Datalator\DatalatorFacade
     */
    protected $facade;

    abstract protected function executeCommand(InputInterface $input, OutputInterface $output): ?int;

    public function setFacade(DatalatorFacadeInterface $facade): void
    {
        $this->facade = $facade;
    }

    protected function getFacade(): DatalatorFacadeInterface
    {
        if ($this->facade === null) {
            $this->facade = new DatalatorFacade();
        }

        return $this->facade;
    }

    protected function configure(): void
    {
        $this
            ->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->setDefinition([
                new InputOption(static::OPTION_SCHEMA, 's', InputOption::VALUE_OPTIONAL, 'Schema name', 'default'),
                new InputOption(static::OPTION_DATA, 'd', InputOption::VALUE_OPTIONAL, 'Data name', 'default'),
                new InputOption(static::OPTION_SCHEMA_PATH, 'sp', InputOption::VALUE_OPTIONAL, 'Directory containing schema files', 'schema/'),
                new InputOption(static::OPTION_DATA_PATH, 'dp', InputOption::VALUE_OPTIONAL, 'Directory containing data files', 'data/'),
                new InputOption(static::OPTION_PATH, 'p', InputOption::VALUE_OPTIONAL, 'Base path to be used when schema and data paths are relative', null),
                new InputOption(static::OPTION_MODULES, 'm', InputOption::VALUE_OPTIONAL, 'Modules', []),
            ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $configurator = $this->buildConfigurator($input);

        if (!$input->hasParameterOption('-q', true) ||
            !$input->hasParameterOption('--quiet', true)) {
            $info = \sprintf(
                'Datalator [%s] schema:%s data:%s',
                static::COMMAND_NAME,
                $configurator->requireSchema(),
                $configurator->requireData()
            );

            $output->writeln($info);
        }

        return $this->executeCommand($input, $output);
    }

    protected function buildConfigurator(InputInterface $input): LoaderConfigurator
    {
        $config = $this->getDotData($input);

        $configuration = (new LoaderConfigurator())
            ->fromArray($config);

        return $configuration;
    }

    protected function getDotData(InputInterface $input): array
    {
        $config = $this->getDotConfig($input);

        $path = $input->getOption(static::OPTION_PATH);
        if (!$path) {
            $path = \getcwd();
        }
        $path = \rtrim($path, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;

        $arguments = [
            'schema' => $input->getOption(static::OPTION_SCHEMA),
            'data' => $input->getOption(static::OPTION_DATA),
            'schemaPath' => $path . \trim($input->getOption(static::OPTION_SCHEMA_PATH)),
            'dataPath' => $path . \trim($input->getOption(static::OPTION_DATA_PATH)),
            'modules' => $input->getOption(static::OPTION_MODULES),
        ];

        $result = \array_merge($arguments, $config);

        return $result;
    }

    protected function getDotConfig(InputInterface $input): array
    {
        $dotPath = $input->getOption(static::OPTION_PATH);
        if (!$dotPath) {
            $dotPath = \rtrim(\getcwd(), \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
        }

        $config = [];
        $configFile = $dotPath . '.datalator';
        if (\is_file($configFile)) {
            $config = \parse_ini_file($configFile, false) ?: [];
        }

        return $config;
    }
}
