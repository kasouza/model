<?php

namespace Kaso\Model\Query;

class Where
{
    public function __construct(
        private string $key,
        private ?string $operator,
        private mixed $value
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }

    public function getOperator(): ?string
    {
        return $this->operator;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
