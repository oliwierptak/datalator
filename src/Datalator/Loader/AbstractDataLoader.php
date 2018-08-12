<?php

declare(strict_types = 1);

namespace Datalator\Loader;

use Datalator\Data\DataSourceInterface;
use Datalator\Finder\FileLoaderInterface;
use Datalator\Popo\LoaderConfigurator;
use Symfony\Component\Finder\SplFileInfo;

abstract class AbstractDataLoader implements DataLoaderInterface
{
    const TYPE = 'undefined';

    /**
     * @var \Datalator\Finder\FileLoaderInterface
     */
    protected $fileLoader;

    abstract protected function buildDataItem(SplFileInfo $file): DataSourceInterface;

    public function __construct(FileLoaderInterface $fileLoader)
    {
        $this->fileLoader = $fileLoader;
    }

    public function load(LoaderConfigurator $configurator): array
    {
        $fileCollection = $this->getFileCollection($configurator);

        $result = [];
        foreach ($fileCollection as $dataFile) {
            $name = $this->getModuleName($dataFile);
            $data = $this->buildDataItem($dataFile);

            $result[$name][] = $data;
        }

        return $result;
    }

    /**
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    protected function getFileCollection(LoaderConfigurator $configurator): array
    {
        $enabledModulesPattern = \implode('|', $configurator->requireModules());

        $pattern = \sprintf(
            "@^%s/${enabledModulesPattern}/(.*)$@",
            $configurator->requireSchema()
        );

        $fileCollection = $this->fileLoader->load(
            $configurator->requireDataPath(),
            $pattern,
            '*.' . \strtolower($configurator->requireDataLoaderType())
        );

        return $fileCollection;
    }

    protected function getModuleName(SplFileInfo $schemaFile): string
    {
        $nameTokens = \explode(\DIRECTORY_SEPARATOR, $schemaFile->getRelativePath());
        $name =  \array_pop($nameTokens);

        return $name;
    }
}
