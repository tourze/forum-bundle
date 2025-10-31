<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum ThreadMediaType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    // article/ITEM是原系统的，不知道干啥用的
    case IMAGE = 'image';
    case VIDEO = 'video';
    case ARTICLE = 'article';
    case ITEM = 'ITEM';

    public function getLabel(): string
    {
        return match ($this) {
            self::IMAGE => '图片',
            self::VIDEO => '视频',
            self::ARTICLE => 'article',
            self::ITEM => 'item',
        };
    }
}
