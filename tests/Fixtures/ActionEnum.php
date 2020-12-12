<?php


namespace PaLabs\Tests\Enum\Fixtures;


use PaLabs\Enum\Enum;

class ActionEnum extends Enum
{
    public static ActionEnum $VIEW, $EDIT;
    public static ActionEnum $DELETE;
}
ActionEnum::init();