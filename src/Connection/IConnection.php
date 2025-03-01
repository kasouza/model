<?php

namespace Kaso\Model\Connection;

use Kaso\Model\PreparedStatement\IPreparedStatement;
use Kaso\Model\Query\BaseQuery;

interface IConnection
{
    public function close(): void;
}
