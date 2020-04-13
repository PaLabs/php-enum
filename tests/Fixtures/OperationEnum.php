<?php


namespace PaLabs\Tests\Enum\Fixtures;


use PaLabs\Enum\Enum;

abstract class OperationEnum extends Enum
{
    public static OperationEnum $SUM, $SUB, $DIV;

    protected static function initValues(): void
    {
        self::$SUM = new class extends OperationEnum {

            public function exec(float $a, float $b): float
            {
                return $a + $b;
            }
        };
        self::$SUB = new class extends OperationEnum {

            public function exec(float $a, float $b): float
            {
                return $a - $b;
            }
        };

        self::$DIV = new class extends OperationEnum {

            public function exec(float $a, float $b): float
            {
                return $a / $b;
            }
        };

    }

    public abstract function exec(float $a, float $b): float;
}

OperationEnum::init();

