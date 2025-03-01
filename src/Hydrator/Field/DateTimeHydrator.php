<?php

namespace Kaso\Model\Hydrator\Field;

use DateTime;

class DateTimeHydrator implements IFieldHydrator
{
    public function hydrate($value)
    {
        return new DateTime($value);
    }

    public function extract($value)
    {
        return $value->format("Y-m-d H:i:s");
    }
}
