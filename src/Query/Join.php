<?php

namespace Kaso\Model\Query;

use Kaso\Model\Query\JoinType;

class Join
{
    public function __construct(
        private string $table,
        private string $onLeft,
        private string $onRight,
        private JoinType $type
    ) {}

    public function getTable() {
        return $this->table;
    }

    public function getOnLeft() {
        return $this->onLeft;
    }

    public function getOnRight() {
        return $this->onRight;
    }

    public function getType() {
        return $this->type;
    }
}
