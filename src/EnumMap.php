<?php

namespace PaLabs\Enum;


use Exception;
use Iterator;
use LogicException;

class EnumMap implements Iterator
{
    private string $enumClass;
    private array $valueMap = [];

    public function  __construct(string $enumClass, array $data = []) {
        $this->enumClass = $enumClass;

        foreach($data as $item) {
            /** @var Enum $enum */
            $enum = $item[0];
            $value = $item[1];

            if(!$enum instanceof $enumClass) {
                throw new Exception(sprintf('Enum in map bust be instance of %s', $this->enumClass));
            }
            $this->valueMap[$enum->name()] = $value;
        }
    }

    public static function fromValues(...$values): EnumMap {
        if(count($values) === 0) {
            throw new LogicException('Values must me set');
        }
        $enumClass = get_class($values[0][0]);
        return new EnumMap($enumClass, $values);
    }

    /**
     * @param Enum $enum
     * @param $value
     * @return $this
     * @throws Exception
     */
    public function attach(Enum $enum, $value): self {
        if(!$enum instanceof $this->enumClass) {
            throw new Exception(sprintf('Enum in map bust be instance of %s', $this->enumClass));
        }
        $this->valueMap[$enum->name()] = $value;
        return $this;
    }

    public function has(Enum $enum): bool {
        return array_key_exists($enum->name(), $this->valueMap);
    }

    public function get(Enum $enum) {
        $value = $enum->name();
        if(!isset($this->valueMap[$value])) {
            throw new \OutOfBoundsException(sprintf('Enum %s not exist in map', $enum->name()));
        }
        return $this->valueMap[$value];
    }

    public function values(): array {
        return array_values($this->valueMap);
    }

    public function rewind()
    {
        reset($this->valueMap);
    }

    public function current()
    {
        return current($this->valueMap);
    }

    public function key()
    {
        /** @var Enum $enum */
        $enum = $this->enumClass;
        $value = key($this->valueMap);
        return $enum::valueOf($value);
    }

    public function next()
    {
        return next($this->valueMap);
    }

    public function valid()
    {
        $key = key($this->valueMap);
        return ($key !== null && $key !== false);
    }

    public function count(): int {
        return count($this->valueMap);
    }
}