<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum LikeType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case THREAD = 'thread';
    case COMMENT = 'comment';

    public function getLabel(): string
    {
        return match ($this) {
            self::THREAD => '点赞帖子',
            self::COMMENT => '点赞评论',
        };
    }
}
