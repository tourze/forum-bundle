<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum ThreadState: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case AUDITING = 'auditing';
    case AUDIT_PASS = 'audit_pass';
    case AUDIT_REJECT = 'audit_reject';
    case USER_DELETE = 'user_delete';

    public function getLabel(): string
    {
        return match ($this) {
            self::AUDITING => '审核中',
            self::AUDIT_PASS => '审核通过',
            self::AUDIT_REJECT => '驳回',
            self::USER_DELETE => '用户删除',
        };
    }
}
