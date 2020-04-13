<?php

namespace PaLabs\Tests\Enum;

use Exception;
use PaLabs\Tests\Enum\Fixtures\ActionEnum;
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
}