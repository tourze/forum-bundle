<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum BadgeType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case THREAD = 'thread';
    case THREAD_COMMENT = 'thread_comment';
    case THREAD_LIKE = 'thread_like';
    case THREAD_COLLECT = 'thread_collect';
    case INVITE = 'invite';
    case SHARE = 'share';
    case CHECKIN = 'checkin';
    case FANS = 'fans';

    public function getLabel(): string
    {
        return match ($this) {
            self::THREAD => '发布帖子',
            self::THREAD_COMMENT => '发布评论',
            self::THREAD_LIKE => '点赞',
            self::THREAD_COLLECT => '收藏',
            self::INVITE => '邀请好友',
            self::SHARE => '分享',
            self::CHECKIN => '签到',
            self::FANS => '粉丝关注',
        };
    }
}
