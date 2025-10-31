<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum ThreadType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case USER_THREAD = 'user_thread';
    case TOPIC_THREAD = 'topic_thread';
    case ACTIVITY_THREAD = 'activity_thread';

    public function getLabel(): string
    {
        return match ($this) {
            self::USER_THREAD => '用户帖子',
            self::TOPIC_THREAD => '话题主贴',
            self::ACTIVITY_THREAD => '活动主贴',
        };
    }
}
