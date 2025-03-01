<?php

namespace Kaso\Model\Hydrator\Field;

class FloatHydrator implements IFieldHydrator
{
    public function hydrate($value)
    {
        return floatval($value);
    }

    public function extract($value)
    {
        return $value;
    }
}
