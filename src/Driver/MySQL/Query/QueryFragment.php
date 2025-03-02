<?php

namespace Kaso\Model\Driver\MySQL\Query;

class QueryFragment
{
    public function __construct(
        private string $queryString = "",
        private array $params = []
    ) {}

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
