<?php

namespace Kaso\Model\Hydrator\Field;

class IntHydrator implements IFieldHydrator
{
    public function hydrate($value)
    {
        return intval($value);
    }

    public function extract($value)
    {
        return $value;
    }
}
