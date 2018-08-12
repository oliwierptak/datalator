<?php

declare(strict_types = 1);

namespace Datalator;

use Datalator\Popo\LoaderConfigurator;
use Datalator\Popo\ReaderConfigurator;
use Datalator\Popo\ReaderValue;

interface DatalatorFacadeInterface
{
    public function setFactory(DatalatorFactoryInterface $factory): void;

    /**
     * Specification:
     * - Drops database if it exists.
     * - Creates new database.
     *
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @return void
     */
    public function create(LoaderConfigurator $configurator): void;

    /**
     * Specification:
     * - Drops database if it exists.
     *
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @return void
     */
    public function drop(LoaderConfigurator $configurator): void;

    /**
     * Specification:
     * - Drops database if it exists.
     * - Creates database if it does not exist.
     * - Populates database with fixture data.
     * - Imports extra data based on the collection of $importConfiguratorCollection.
     *
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param \Datalator\Popo\LoaderConfigurator[] $importConfiguratorCollection
     *
     * @return void
     */
    public function import(LoaderConfigurator $configurator, array $importConfiguratorCollection): void;

    /**
     * Specification:\
     * - Drops database if it exists.
     * - Creates database if it does not exist.
     * - Populates database with fixture data.
     *
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     *
     * @return void
     */
    public function populate(LoaderConfigurator $configurator): void;

    /**
     * Specification:
     * - Reads value from the database.
     * - The returned value is selected based on the identity value and column
     * - The query value and column can be used to narrow down the returned value.
     * - Returns all columns if query column is empty, selected column value otherwise.
     *
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param \Datalator\Popo\ReaderConfigurator $readerConfigurator
     *
     * @return \Datalator\Popo\ReaderValue
     */
    public function readFromSchema(LoaderConfigurator $configurator, ReaderConfigurator $readerConfigurator): ReaderValue;

    /**
     * Specification:
     * - Reads value from the fixture file defined by dataLoaderType type, default is CSV format.
     * - The returned value is selected based on the identity value and column
     * - The query value and column can be used to narrow down the returned value.
     * - Returns all columns if query column is empty, selected column value otherwise.
     *
     * @param \Datalator\Popo\LoaderConfigurator $configurator
     * @param \Datalator\Popo\ReaderConfigurator $readerConfigurator
     *
     * @return \Datalator\Popo\ReaderValue
     */
    public function readFromData(LoaderConfigurator $configurator, ReaderConfigurator $readerConfigurator): ReaderValue;
}
