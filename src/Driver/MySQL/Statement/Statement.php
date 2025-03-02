<?php

namespace Kaso\Model\Driver\MySQL\Statement;

use Exception;
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
        if (false === $this->stmt->execute($params)) {
            throw new Exception($this->stmt->errorCode());
        }
    }

    public function fetchRow(): ?object
    {
        $result = $this->stmt->fetchObject();
        if (false === $result) {
            return null;
        }

        return $result;
    }

    public function fetchAllRows(): array
    {
        $result = $this->stmt->fetchAll(PDO::FETCH_OBJ);
        if (false === $result) {
            return [];
        }

        return $result;
    }

    public function rowCount(): int {
        return $this->stmt->rowCount();
    }
}
