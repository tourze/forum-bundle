<?php

namespace ForumBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use WechatMiniProgramAuthBundle\Event\CodeToSessionResponseEvent;

/**
 * 处理登录
 */
class JsonRpcSubscriber
{
    #[AsEventListener]
    public function onCodeToSessionResponseEvent(CodeToSessionResponseEvent $event): void
    {
        // 所有用户都要关注官方账号
        if (!$event->isNewUser()) {
            return;
        }

        $user = $event->getBizUser();

        // 需要实现官方用户获取逻辑
        // 等待官方用户服务可用后，实现关注关系逻辑：
        // 1. 从适当的服务获取官方用户
        // 2. 检查关注关系是否已存在
        // 3. 创建新的关注关系（如果需要）
        // 4. 持久化并刷新更改
    }
}
