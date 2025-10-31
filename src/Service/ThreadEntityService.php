<?php

namespace ForumBundle\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Event\ThreadAuditPassEvent;
use ForumBundle\Event\ThreadAuditRejectEvent;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\LockServiceBundle\Model\LockEntity;
use Tourze\UserIDBundle\Model\SystemUser;
use Tourze\UserServiceContracts\UserManagerInterface;
use Yiisoft\Arrays\ArrayHelper;

final readonly class ThreadEntityService
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private Security $security,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function handleBeforeCreate(Thread $thread, UserManagerInterface $userManager): void
    {
        // UserManagerInterface doesn't have getUserById method
        // Need to use appropriate method or service
        throw new \RuntimeException('需要实现获取官方用户的逻辑');
    }

    /**
     * @param array<string, mixed> $form
     * @param array<string, mixed> $record
     */
    public function handleAfterEdit(Thread $thread, array $form, array $record): void
    {
        $status = ArrayHelper::getValue($form, 'status');
        $oldStatus = ArrayHelper::getValue($record, 'status');

        if ($status === $oldStatus) {
            return;
        }

        $this->dispatchAuditEvent($thread, $status);
    }

    private function dispatchAuditEvent(Thread $thread, mixed $status): void
    {
        $eventConfig = $this->getAuditEventConfig($status);
        if (null === $eventConfig) {
            return;
        }

        [$eventClass, $message] = $eventConfig;
        /** @var ThreadAuditPassEvent|ThreadAuditRejectEvent $event */
        $event = new $eventClass();
        $event->setThread($thread);

        $currentUser = $this->security->getUser();
        if (null !== $currentUser) {
            $event->setSender($currentUser);
        }

        $event->setReceiver($thread->getUser() ?? SystemUser::instance());
        $event->setMessage($message);
        $this->eventDispatcher->dispatch($event);
    }

    /**
     * @return array{string, string}|null
     */
    private function getAuditEventConfig(mixed $status): ?array
    {
        return match ($status) {
            ThreadState::AUDIT_PASS->value => [ThreadAuditPassEvent::class, '帖子审核通过'],
            ThreadState::AUDIT_REJECT->value => [ThreadAuditRejectEvent::class, '帖子审核不通过'],
            default => null,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function renderEntityCount(Thread $thread): array
    {
        return [
            'comments' => $thread->getCommentCount(),
            'likes' => $thread->getLikeCount(),
            'collects' => $thread->getCollectCount(),
        ];
    }

    public function renderVideoColumn(Thread $thread): string
    {
        if ($thread->getThreadMedia()->isEmpty()) {
            return '';
        }

        $result = json_encode([
            'label' => '查看视频',
            'url' => 'https://example.com/video/' . $thread->getId(),
        ]);

        return false !== $result ? $result : '';
    }

    public function renderUserPhoneColumn(Thread $thread): string
    {
        // UserInterface doesn't have getMobile method
        $result = json_encode([
            'value' => '',
        ]);

        return false !== $result ? $result : '';
    }

    public function getOtherData(Thread $thread): string
    {
        $data = [
            'share_url' => $this->urlGenerator->generate('default'),
        ];

        $user = $thread->getUser();
        if (null !== $user) {
            $data['share_url'] .= '?invitedBy=' . urlencode($user->getUserIdentifier());
        }

        // UserInterface doesn't have getMobile method
        // Skip mobile phone retrieval
        return '';
    }

    public function generateLockResource(Thread $thread): string
    {
        return 'lock_forum_thread_' . $thread->getId();
    }
}
