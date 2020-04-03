<?php


namespace PaLabs\Tests\Enum;


use PaLabs\Enum\Enum;

abstract class OperationEnum extends Enum
{
    public static OperationEnum $SUM, $SUB, $DIV;

    public abstract function exec(float $a, float $b): float;
}

OperationEnum::$SUM = new class extends OperationEnum {

    public function exec(float $a, float $b): float
    {
        return $a + $b;
    }
};

OperationEnum::$SUB = new class extends OperationEnum {

    public function exec(float $a, float $b): float
    {
        return $a - $b;
    }
};

OperationEnum::$DIV = new class extends OperationEnum {

    public function exec(float $a, float $b): float
    {
        return $a / $b;
    }
};

OperationEnum::init();

