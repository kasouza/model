<?php

namespace Kaso\Model\Query;

enum JoinType: string
{
    case LEFT = "LEFT";
    case INNER = "INNER";
}
