<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum ThreadCommentState: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case AUDIT_PASS = 'pass';
    case SYSTEM_DELETE = 'system_delete';
    case USER_DELETE = 'user_delete';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUDIT_PASS => '有效',
            self::SYSTEM_DELETE => '系统删除',
            self::USER_DELETE => '用户删除',
        };
    }
}
