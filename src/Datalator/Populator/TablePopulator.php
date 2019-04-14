<?php

declare(strict_types = 1);

namespace Datalator\Populator;

use Datalator\Data\DataSourceInterface;
use Datalator\Popo\ModuleTable;
use Doctrine\DBAL\Connection;

class TablePopulator implements TablePopulatorInterface
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $databaseConnection;

    /**
     * @var \Datalator\Popo\ModuleTable
     */
    protected $table;

    /**
     * @var \Datalator\Data\DataSourceInterface
     */
    protected $dataSource;

    public function __construct(
        Connection $databaseConnection,
        ModuleTable $table,
        DataSourceInterface $dataSource
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->table = $table;
        $this->dataSource = $dataSource;
    }

    public function populateTable(): void
    {
        foreach ($this->dataSource->getData() as $rowData) {
            $row = $this->prepareRow($rowData);

            $this->getConnection()->insert(
                $this->table->getName(),
                $row
            );
        }
    }

    protected function prepareRow(array $row): array
    {
        $row = $this->remapValues($row);

        $escapedRow = [];
        foreach ($row as $name => $value) {
            $name = $this->getConnection()->quoteIdentifier($name);
            $escapedRow[$name] = $value;
        }

        return $escapedRow;
    }

    protected function remapValues(array $data): array
    {
        $mapFunction = function (string $value) {
            $valueToCheck = \strtolower(\trim($value));

            if ($valueToCheck === 'null') {
                return null;
            }

            if ($valueToCheck === 'now()') {
                return (new \DateTime())->format('Y-m-d H:i:s');
            }

            return $value;
        };

        return \array_map($mapFunction, $data);
    }

    protected function getConnection(): Connection
    {
        return $this->databaseConnection;
    }
}
