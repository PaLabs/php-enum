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
        $class = new ReflectionClass($className);

        if (array_key_exists($className, self::$values)) {
            throw new Exception(sprintf("Enum has been already initialized, enum=%s", $className));
        }
        static::initValues($class);

        self::$values[$className] = [];
        self::$valueMap[$className] = [];

        /** @var Enum[] $enumFields */
        $properties = $class->getStaticProperties();
        $enumFields = array_filter($properties, fn($property) => $property instanceof Enum);
        if (count($enumFields) == 0) {
            throw new Exception(sprintf("Enum has not values, enum=%s", $className));
        }

        foreach ($enumFields as $property) {
            if (array_key_exists($property->name(), self::$valueMap[$className])) {
                throw new Exception(sprintf("Duplicate enum value %s from enum %s", $property->name(), $className));
            }

            self::$values[$className][] = $property;
            self::$valueMap[$className][$property->name()] = $property;
        }
    }

    private static function initValues(ReflectionClass $class): void
    {
        $properties = self::properties($class);

        foreach ($properties as $idx => $name) {
            $enumValue = $class->getStaticPropertyValue($name);
            if (!isset($enumValue)) {
                /** @var Enum $enumValue */
                $enumValue = $class->newInstance();
                $class->setStaticPropertyValue($name, $enumValue);
            }
            $enumValue->name = $name;
            $enumValue->ordinal = $idx;
        }
    }

    private static function properties(ReflectionClass $class): array
    {
        $shortClass = $class->getShortName();
        $fileContent = file_get_contents($class->getFileName());

        // use regexp to extract public static properties with type of enum class name
        // [\s]+? - any space (including \n symbol), ? - lazy qualifier
        $pattern = "/public[\s]+?static[\s]+?".$shortClass."[\s]+?([^;]+?);/i";
        preg_match_all($pattern, $fileContent, $matches);
        if(count($matches) < 2) {
            return [];
        }

        $allFields = [];
        foreach($matches[1] as $fieldsStr) {
            $fields = explode(',', $fieldsStr);
            $fields = array_map('trim', $fields);
            $fields = array_map(
                fn(string $fieldName) => substr($fieldName, 1),
                $fields
            );
            $allFields = array_merge($allFields, $fields);
        }

        return $allFields;
    }

}
