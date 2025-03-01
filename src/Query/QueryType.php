<?php

namespace Kaso\Model\Query;

enum QueryType: string
{
    case SELECT = "select";
    case UPDATE = "updated";
}
