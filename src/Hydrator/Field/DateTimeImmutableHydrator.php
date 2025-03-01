<?php

namespace Kaso\Model\Hydrator\Field;

use DateTimeImmutable;

class DateTimeImmutableHydrator implements IFieldHydrator
{
    public function hydrate($value)
    {
        return new DateTimeImmutable($value);
    }

    public function extract($value)
    {
        return $value->format("Y-m-d H:i:s");
    }
}
