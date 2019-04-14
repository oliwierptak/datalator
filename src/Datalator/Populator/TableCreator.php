<?php

declare(strict_types = 1);

namespace Datalator\Populator;

use Datalator\Popo\ModuleTable;
use Doctrine\DBAL\Connection;

class TableCreator implements TableCreatorInterface
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
        ModuleTable $table
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->table = $table;
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

    public function tableExists(): bool
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
