<?php

namespace ForumBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum ThreadRelationType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case CMS_ENTITY = 'cms_entity';

    public function getLabel(): string
    {
        return match ($this) {
            self::CMS_ENTITY => '文章',
        };
    }
}
