<?php

namespace Kaso\Model\Hydrator\Field;

interface IFieldHydrator
{
    public function hydrate($value);
    public function extract($value);
}
