<?php


namespace PaLabs\Tests\Enum;


use OutOfBoundsException;
use PaLabs\Enum\EnumMap;
use PaLabs\Tests\Enum\Fixtures\ActionEnum;
use PHPUnit\Framework\TestCase;

class EnumMapTest extends TestCase
{

    public function testValuesConstructor()
    {
        $map = EnumMap::fromValues(
            [ActionEnum::$EDIT, 1],
            [ActionEnum::$VIEW, 2],
            [ActionEnum::$DELETE, 3]
        );
        $this->assertEquals($map->get(ActionEnum::$EDIT), 1);
        $this->assertEquals($map->get(ActionEnum::$VIEW), 2);
        $this->assertEquals($map->get(ActionEnum::$DELETE), 3);
    }

    public function testIllegalOffset()
    {
        $this->expectException(OutOfBoundsException::class);
        $map = EnumMap::fromValues(
            [ActionEnum::$EDIT, 1],
            [ActionEnum::$VIEW, 2],
        );

        $map->get(ActionEnum::$DELETE);
    }
}