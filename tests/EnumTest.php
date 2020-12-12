<?php

namespace PaLabs\Tests\Enum;

use Exception;
use PaLabs\Tests\Enum\Fixtures\ActionEnum;
use PaLabs\Tests\Enum\Fixtures\ExtendedActionEnum;
use PaLabs\Tests\Enum\Fixtures\OperationEnum;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testValuesCount()
    {
        $values = ActionEnum::values();
        $this->assertCount(3, $values);
    }

    public function testOrdinal() {
        $this->assertEquals(0, ActionEnum::$VIEW->ordinal());
        $this->assertEquals(1, ActionEnum::$EDIT->ordinal());
        $this->assertEquals(2, ActionEnum::$DELETE->ordinal());
    }

    public function testName() {
        $this->assertEquals('VIEW', ActionEnum::$VIEW->name());
        $this->assertEquals('EDIT', ActionEnum::$EDIT->name());
        $this->assertEquals('DELETE', ActionEnum::$DELETE->name());
    }

    public function testCorrectValueOf()
    {
        $this->assertSame(ActionEnum::valueOf('VIEW'), ActionEnum::$VIEW);
    }

    public function testAbsentValue()
    {
        $this->expectException(Exception::class);
        $enum = ActionEnum::valueOf('NON_EXISTING_VALUE');
    }

    public function testEquals() {
        $this->assertTrue(ActionEnum::valueOf('VIEW')->equals(ActionEnum::$VIEW));
    }

    public function testEnumMethod() {
        $this->assertEquals(3, OperationEnum::$SUM->exec(1, 2));
        $this->assertEquals(-1, OperationEnum::$SUB->exec(1, 2));
    }

    public function testExtendedEnum()
    {
        $actions = ActionEnum::values();
        $this->assertEquals(ActionEnum::class, get_class($actions[0]));
        $this->assertEquals(ActionEnum::class, get_class($actions[1]));
        $this->assertEquals(ActionEnum::class, get_class($actions[2]));

        $this->assertSame(ActionEnum::$VIEW, $actions[0]);
        $this->assertSame(ActionEnum::$EDIT, $actions[1]);
        $this->assertSame(ActionEnum::$DELETE, $actions[2]);


        $extendedActions = ExtendedActionEnum::values();
        $this->assertEquals(ActionEnum::class, get_class($extendedActions[0]));
        $this->assertEquals(ActionEnum::class, get_class($extendedActions[1]));
        $this->assertEquals(ActionEnum::class, get_class($extendedActions[2]));
        $this->assertEquals(ExtendedActionEnum::class, get_class($extendedActions[3]));

        $this->assertSame(ActionEnum::$VIEW, $extendedActions[0]);
        $this->assertSame(ActionEnum::$EDIT, $extendedActions[1]);
        $this->assertSame(ActionEnum::$DELETE, $extendedActions[2]);
        $this->assertSame(ExtendedActionEnum::$CLONE, $extendedActions[3]);

        $this->assertEquals(ActionEnum::$DELETE->ordinal(), 2);
        $this->assertEquals(ExtendedActionEnum::$DELETE->ordinal(), 2);
        $this->assertEquals(ExtendedActionEnum::$CLONE->ordinal(), 3);

        $this->assertSame(ActionEnum::$VIEW, ExtendedActionEnum::$VIEW);
    }
}