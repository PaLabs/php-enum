<?php

namespace PaLabs\Enum;

use Exception;
use ReflectionClass;

class Enum
{
    private static array $values = [];
    private static array $valueMap = [];

    private string $name;
    private int $ordinal;

    public function name(): string
    {
        return $this->name;
    }

    public function ordinal(): int {
        return $this->ordinal;
    }

    public function __toString(): string
    {
        return strtolower($this->name);
    }

    public function equals(?Enum $other): bool
    {
        if ($other === null) {
            return false;
        }
        return $this === $other;
    }

    protected static function initValues(): void {
        // method can be overrided
    }

    /**
     * @return Enum[]
     * @throws Exception
     */
    public static function values(): array
    {
        $className = get_called_class();
        if (!array_key_exists($className, self::$values)) {
            throw new Exception(sprintf("Enum is not initialized, enum=%s", $className));
        }
        return self::$values[$className];
    }

    public static function valueOf(string $name): Enum
    {
        $className = get_called_class();
        if (!isset(self::$valueMap[$className])) {
            static::init();
        }
        if (!isset(self::$valueMap[$className][$name])) {
            throw new Exception(sprintf('Unknown enum value %s for enum %s', $name, $className));
        }
        return self::$valueMap[$className][$name];
    }

    public static function init(): void
    {
        $className = get_called_class();

        if (array_key_exists($className, self::$values)) {
            return;
        }
        static::initValues();

        $properties = self::properties($className);
        self::initEmptyValues($properties);

        self::$values[$className] = [];
        self::$valueMap[$className] = [];

        // dont need declaring classes here -- we are filling only values of current class
        foreach ($properties as [ $_, $propertyName ]) {
            if (array_key_exists($propertyName, self::$valueMap[$className])) {
                throw new Exception(sprintf("Duplicate enum value %s from enum %s", $propertyName, $className));
            }

            $propertyValue = $className::$$propertyName;
            self::$values[$className][] = $propertyValue;
            self::$valueMap[$className][$propertyName] = $propertyValue;
        }
    }

    private static function initEmptyValues(array $properties): void
    {
        // className here is a declaring class of a property
        // Properties of parent classes should be instances of those parent classes.
        foreach ($properties as $idx => [ $className, $name ]) {
            if(!isset($className::$$name)) {
                /** @var Enum $enumValue */
                $enumValue = new $className();
                $className::$$name = $enumValue;
            } else {
                $enumValue = $className::$$name;
            }

            $enumValue->name = $name;
            $enumValue->ordinal = $idx;
        }
    }

    private static function properties($className)
    {
        // array of lists of properties of each class in hierarchy
        $enumValues = [];
        // array of classes in hierarchy from current to parents to root
        $classHierarchy = [];

        $class = new ReflectionClass($className);
        $properties = $class->getProperties(\ReflectionProperty::IS_STATIC | \ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $type = $property->getType();

            if (!$type instanceof \ReflectionNamedType) {
                continue;
            }

            $matched = false;
            if ($type->getName() === $class->getName()) {
                $matched = true;
            } else if ($type->getName() === 'self' || $type->getName() === 'parent') {
                // valid as of https://wiki.php.net/rfc/typed_properties_v2
                $matched = true;
            } else if (class_exists($type->getName()) && is_subclass_of($class->getName(), $type->getName())) {
                $matched = true;
            }

            if ($matched) {
                // Properties in ReflectionClass->getProperties are sorted by declaring class.
                // While iterating, when we see a new declaring class, we add it to the end of the list.
                $declaringClassName = $property->getDeclaringClass()->getName();
                if (!isset($enumValues[$declaringClassName])) {
                    $enumValues[$declaringClassName] = [];
                    $classHierarchy[] = $declaringClassName;
                }
                $enumValues[$declaringClassName][] = $property->getName();
            }
        }

        // Now we transform collected structure into a plain list of [ declaring-class, property-name ]
        // where properties from top-most parent class go first, next down the hierarchy to the current class
        // The ordering is important for setting `ordinal` numbers later.
        $plainList = [];
        foreach (array_reverse($classHierarchy) as $declaringClassName) {
            foreach ($enumValues[$declaringClassName] as $value) {
                $plainList[] = [ $declaringClassName, $value ];
            }
        }

        return $plainList;
    }
}
