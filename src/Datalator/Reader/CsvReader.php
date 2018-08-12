<?php

declare(strict_types = 1);

namespace Datalator\Reader;

use Datalator\Popo\ReaderConfigurator;
use Datalator\Popo\ReaderValue;

class CsvReader implements ReaderInterface
{
    /**
     * @var \Datalator\Popo\LoaderConfigurator
     */
    protected $loaderConfigurator;

    /**
     * @var \Datalator\Loader\DataLoaderInterface
     */
    protected $dataLoader;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Datalator\Loader\DataLoaderInterface $dataLoader,
        \Psr\Log\LoggerInterface $logger,
        \Datalator\Popo\LoaderConfigurator $loaderConfigurator
    ) {
        $this->dataLoader = $dataLoader;
        $this->logger = $logger;
        $this->loaderConfigurator = $loaderConfigurator;
    }

    public function read(ReaderConfigurator $configurator): ReaderValue
    {
        $value = (new ReaderValue());

        $data = $this->dataLoader->load($this->loaderConfigurator);

        /**
         * @var \Datalator\Data\DataSourceInterface[] $files
         */
        foreach ($data as $moduleName => $files) {
            foreach ($files as $dataSource) {
                if ($dataSource->getName() !== $configurator->getSource()) {
                    continue;
                }

                foreach ($dataSource->getData() as $row) {
                    $id = null;
                    if (\array_key_exists($configurator->getIdentityColumn(), $row)) {
                        $id = $row[$configurator->getIdentityColumn()];
                    }
                    if (!$id) {
                        continue;
                    }

                    if (\strcasecmp($id, $configurator->getIdentityValue()) !== 0) {
                        continue;
                    }

                    if (\array_key_exists($configurator->getQueryColumn(), $row)) {
                        $value->setDataValue($row[$configurator->getQueryColumn()]);
                        return $value;
                    }
                }
            }
        }

        return $value;
    }
}
