<?php

namespace ForumBundle\Util;

use ForumBundle\Enum\BadgeType;

class BadgeUtil
{
    public static function genConditionText(string $type, int $done, int $total): string
    {
        return match ($type) {
            BadgeType::THREAD->value => "升级需累计完成{$done}/{$total}次发帖",
            BadgeType::THREAD_LIKE->value => "升级需累计完成{$done}/{$total}次点赞",
            BadgeType::THREAD_COMMENT->value => "升级需累计完成{$done}/{$total}次评论",
            BadgeType::INVITE->value => "升级需累计完成{$done}/{$total}次邀请",
            BadgeType::CHECKIN->value => "升级需累计完成{$done}/{$total}次签到",
            BadgeType::FANS->value => "升级需累计完成{$done}/{$total}个粉丝",
            default => '',
        };
    }
}
