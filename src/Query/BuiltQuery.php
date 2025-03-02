<?php

namespace Kaso\Model\Query;

class BuiltQuery implements IBuiltQuery
{
    public function __construct(
        private string $builtString,
        private array $params
    ) {}

    public function getParams(): array
    {
        return $this->params;
    }

    public function getBuiltString(): string
    {
        return $this->builtString;
    }
}
