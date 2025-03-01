<?php

namespace Kaso\Model\Driver\MySQL\Query;

use Kaso\Model\Query\JoinType;

class Join
{
    public function __construct(
        public string $table,
        public string $on,
        public JoinType $type
    ) {}
}
