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

    public function createTable(): void
    {
        $this
            ->getConnection()
            ->executeQuery(
                $this->table->getSql()
            );
    }

    public function dropTable(): void
    {
        if (!$this->tableExists()) {
            return;
        }

        $this->getConnection()->getSchemaManager()->dropTable(
            $this->table->getName()
        );
    }

    public function populateTable(): void
    {
        if (!$this->tableExists()) {
            $this->createTable();
        }

        foreach ($this->dataSource->getData() as $rowData) {
            $row = $this->prepareRow($rowData);

            $this->getConnection()->insert(
                $this->table->getName(),
                $row
            );
        }
    }

    /**
     * @param array $data
     *
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    protected function prepareRow(array $data): array
    {
        $dataCount = \count($data);
        $columnCount = \count($this->dataSource->getColumns());

        if ($dataCount !== $columnCount) {
            throw new \UnexpectedValueException('Data and column count does not match');
        }

        $data = $this->remapValues($data);

        $row = \array_combine(
            $this->dataSource->getColumns(),
            $data
        );

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

    protected function tableExists(): bool
    {
        return $this->getConnection()->getSchemaManager()->tablesExist(
            [$this->table->getName()]
        );
    }

    protected function getConnection(): Connection
    {
        return $this->databaseConnection;
    }
}
