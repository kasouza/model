<?php

namespace Kaso\Model\Driver\MySQL\Statement;

use Kaso\Model\Driver\MySQL\Connection\Connection;
use Kaso\Model\Statement\IStatement;

use PDO;
use PDOStatement;

class Statement implements IStatement
{
    protected PDOStatement $stmt;

    public function __construct(
        protected Connection $connection,
        protected string $queryString
    ) {
        $this->stmt = $connection->getHandle()->prepare($queryString);
    }

    public function execute(array $params): void
    {
        $this->stmt->execute($params);
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
