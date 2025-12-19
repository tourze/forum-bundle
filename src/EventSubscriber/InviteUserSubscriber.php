<?php

namespace ForumBundle\EventSubscriber;

use ForumBundle\Enum\BadgeType;
use ForumBundle\Service\BadgeService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;
use WechatMiniProgramShareBundle\Event\InviteUserEvent;

/**
 * 邀请用户
 */
#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'forum')]
final readonly class InviteUserSubscriber
{
    public function __construct(
        private LoggerInterface $logger,
        private BadgeService $badgeService,
        private UserManagerInterface $userManager,
    ) {
    }

    #[AsEventListener]
    public function onInviteUserEvent(InviteUserEvent $event): void
    {
        $log = $event->getInviteVisitLog();
        $shareOpenId = $log->getShareOpenId();
        $visitOpenId = $log->getVisitOpenId();

        if (null === $shareOpenId || null === $visitOpenId) {
            return;
        }

        $user = $this->loadUserSafely($shareOpenId);
        $visitUser = $this->loadUserSafely($visitOpenId);

        if (null !== $user && null !== $visitUser) {
            $this->handleBadgeUpgrade($user);
        }
    }

    private function loadUserSafely(string $identifier): ?UserInterface
    {
        try {
            return $this->userManager->loadUserByIdentifier($identifier);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function handleBadgeUpgrade(UserInterface $user): void
    {
        try {
            $this->badgeService->upgrade($user, BadgeType::INVITE);
        } catch (\Throwable $exception) {
            $this->logger->error('处理徽章升级逻辑失败', [
                'error' => $exception,
            ]);
        }
    }
}
