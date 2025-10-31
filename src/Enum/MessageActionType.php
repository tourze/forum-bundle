<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MessageActionType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case DRAW = 'draw';
    case EXIT = 'exit';
    case KICK = 'kick';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAW => '抽奖',
            self::EXIT => '退出',
            self::KICK => '踢出',
        };
    }
}
