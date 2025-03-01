<?php

namespace Kaso\Model\Driver\MySQL\Statement;

use Kaso\Model\Driver\MySQL\Connection\Connection;
use Kaso\Model\Driver\MySQL\Query\Query;
use Kaso\Model\Statement\IStatement;

use PDO;
use PDOStatement;

class Statement implements IStatement
{
    protected PDOStatement $stmt;

    public function __construct(
        protected Query $query,
        protected Connection $connection
    ) {
        $this->stmt = $connection->getHandle()->prepare($query->build());
    }

    public function execute(): void
    {
        $this->stmt->execute();
    }

    public function fetchRow(): object
    {
        return $this->stmt->fetchObject();
    }

    public function fetchAllRows(): array
    {
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function rowCount(): int {
        return $this->stmt->rowCount();
    }
}
