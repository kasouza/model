<?php

namespace Kaso\Model\Hydrator;

use Exception;
use Kaso\Model\Hydrator\Field\BoolHydrator;
use Kaso\Model\Hydrator\Field\DateTimeHydrator;
use Kaso\Model\Hydrator\Field\DateTimeImmutableHydrator;
use Kaso\Model\Hydrator\Field\FloatHydrator;
use ReflectionClass;
use stdClass;

use Kaso\Model\Hydrator\Field\IFieldHydrator;
use Kaso\Model\Hydrator\Field\IntHydrator;

class Hydrator implements IHydrator
{
    protected ReflectionClass $reflectionClass;

    /** @var IFieldHydrator $fieldHydrators */
    protected array $fieldHydrators = [];

    public function __construct(protected string $entityClass)
    {
        if (!class_exists($this->entityClass)) {
            throw new Exception("Invalid entity class");
        }

        $this->reflectionClass = new ReflectionClass($this->entityClass);

        $this->addFieldHydrator("bool", new BoolHydrator);
        $this->addFieldHydrator("int", new IntHydrator);
        $this->addFieldHydrator("float", new FloatHydrator);
        $this->addFieldHydrator("DateTime", new DateTimeHydrator);
        $this->addFieldHydrator("DateTimeImmutable", new DateTimeImmutableHydrator);
    }

    public function hydrate(object $obj): object
    {
        $hydrated = new $this->entityClass;
        $fields = $this->reflectionClass->getProperties();

        foreach ($fields as $field) {
            $name = $field->getName();
            $type = $field->getType();
            $typeName = $type->getName();

            if (!isset($obj->$name)) {
                if ($type->allowsNull()) {
                    $hydrated->$name = null;
                } else {
                    throw new Exception("{$name} must not be empty");
                }
            } else {
                $value = $obj->$name;

                $hydrator = $this->getFieldHydrator($typeName);
                if (!empty($hydrator)) {
                    $value = $hydrator->hydrate($value);
                }

                $hydrated->$name = $value;
            }
        }

        return $hydrated;
    }

    public function extract(object $obj): object
    {
        $extracted = new stdClass;

        $fields = $this->reflectionClass->getProperties();

        foreach ($fields as $field) {
            $name = $field->getName();
            $type = $field->getType();
            $typeName = $type->getName();

            if (!isset($obj->$name)) {
                $extracted->$name = $obj->$name;
            } else {
                $value = $obj->$name;

                $hydrator = $this->getFieldHydrator($typeName);
                if (!empty($hydrator)) {
                    $value = $hydrator->extract($value);
                }

                $extracted->$name = $value;
            }
        }

        return $extracted;
    }

    public function addFieldHydrator(string $type, IFieldHydrator $hydrator)
    {
        $this->fieldHydrators[$type] = $hydrator;
    }

    public function getFieldHydrator(string $type): ?IFieldHydrator
    {
        return $this->fieldHydrators[$type] ?? null;
    }
}
