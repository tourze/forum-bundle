<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MessageType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case SYSTEM_NOTIFICATION = 'system_notification';
    case REPLY = 'reply';
    case FOLLOW = 'follow';
    case PRIVATE_LETTER = 'private_letter';
    case LIKE_THREAD = 'like_thread';
    case LIKE_THREAD_COMMENT = 'like_thread_comment';
    case COLLECT_THREAD = 'collect_thread';

    public function getLabel(): string
    {
        return match ($this) {
            self::SYSTEM_NOTIFICATION => '系统通知',
            self::REPLY => '回复',
            self::FOLLOW => '关注',
            self::PRIVATE_LETTER => '私信',
            self::LIKE_THREAD => '点赞帖子',
            self::LIKE_THREAD_COMMENT => '点赞评论',
            self::COLLECT_THREAD => '收藏帖子',
        };
    }
}
