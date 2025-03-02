<?php

namespace Kaso\Model\Result;

use Kaso\Model\Hydrator\IHydrator;
use Kaso\Model\Statement\IStatement;

class Result implements IResult
{
    public function __construct(
        protected IStatement $statement,
        protected ?IHydrator $hydrator = null
    ) {}

    public function getFirst(): ?object
    {
        $row = $this->statement->fetchRow();
        if (empty($this->hydrator) || empty($row)) {
            return $row;
        }

        return $this->hydrator->hydrate($row);
    }

    public function getAll(): array
    {
        $rows = $this->statement->fetchAllRows();
        if (empty($this->hydrator)) {
            return $rows;
        }

        return array_map(function ($row) {
            return $this->hydrator->hydrate($row);
        }, $rows);
    }

    public function count(): int
    {
        return $this->statement->rowCount();
    }
}
