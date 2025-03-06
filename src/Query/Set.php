<?php

namespace Kaso\Model\Query;

class Set
{
    public function __construct(
        private string $key,
        private mixed $value
    ) {}

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
