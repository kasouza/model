<?php

namespace Kaso\Model\Hydrator;

interface IHydrator {
    public function hydrate(object $obj): object;
    public function extract(object $obj): object;
}
