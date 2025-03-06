<?php

namespace Kaso\Model\Query;

class RawQuery
{
    public function __construct(
        private string $queryString = "",
        private array $params = []
    ) {}

    public function getQueryString() {
        return $this->queryString;
    }

    public function getParams() {
        return $this->params;
    }
}
