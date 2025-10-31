<?php

namespace ForumBundle\Service;

use ForumBundle\Enum\BadgeType;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Badge 服务暂时禁用，等待 BadgeBundle 依赖
 *
 * 需要添加 BadgeBundle 依赖包或重新实现徽章功能
 */
#[WithMonologChannel(channel: 'forum')]
readonly class BadgeService
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * 升级逻辑 - 暂时禁用
     */
    public function upgrade(UserInterface $user, BadgeType $type): void
    {
        $this->logger->info('BadgeService is temporarily disabled', [
            'user' => $user->getUserIdentifier(),
            'type' => $type->value,
        ]);
        // 等待 BadgeBundle 依赖包实现徽章升级逻辑
    }
}
