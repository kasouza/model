<?php

namespace Kaso\Model\Hydrator\Field;

class BoolHydrator implements IFieldHydrator
{
    public function hydrate($value)
    {
        return boolval($value);
    }

    public function extract($value)
    {
        return $value ? 1 : 0;
    }
}
