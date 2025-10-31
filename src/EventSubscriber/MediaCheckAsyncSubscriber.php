<?php

namespace ForumBundle\EventSubscriber;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Enum\MessageType;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadMediaRepository;
use ForumBundle\Service\NotifyService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use WechatMiniProgramSecurityBundle\Event\MediaCheckAsyncEvent;

/**
 * 处理媒体检测回调事件
 */
#[WithMonologChannel(channel: 'forum')]
readonly class MediaCheckAsyncSubscriber
{
    public function __construct(
        private ThreadMediaRepository $threadMediaRepository,
        private NotifyService $notifyService,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[AsEventListener]
    public function onAfterMediaCheckAsyncCallback(MediaCheckAsyncEvent $event): void
    {
        $mediaCheckLog = $event->getMediaCheckLog();
        // 有风险的图片主贴要下架
        $isRisky = $mediaCheckLog->isRisky();
        if (null === $mediaCheckLog->getMediaUrl() || '' === $mediaCheckLog->getMediaUrl() || true !== $isRisky) {
            $this->logger->debug('不存在违规');

            return;
        }
        $threadMedia = $this->threadMediaRepository->findOneBy([
            'path' => $mediaCheckLog->getMediaUrl(),
        ]);
        if (null !== $threadMedia) {
            $thread = $threadMedia->getThread();
            if (null !== $thread) {
                // UserManagerInterface doesn't have find method
                // Need to implement official user retrieval logic
                $user = null; // 需要实现官方用户获取逻辑
                $thread->setStatus(ThreadState::AUDIT_REJECT);
                $thread->setUpdateTime(CarbonImmutable::now());
                $this->entityManager->persist($thread);
                $this->entityManager->flush();

                $message = new MessageNotification();
                $message->setUser($thread->getUser());
                $message->setType(MessageType::SYSTEM_NOTIFICATION);
                $message->setSender($user);
                $message->setContent('你的帖子存在违规信息被驳回,请你到个人发帖中心处理');
                $threadId = $thread->getId();
                if (null !== $threadId) {
                    $message->setTargetId($threadId);
                }
                $message->setReadStatus(0);
                $message->setPath("/pages/UserPosters/UserPosters?threadId={$thread->getId()}");
                $this->notifyService->send($message);
            }
        }

        // Early return since we don't have proper event data implementation
        // 等待 MediaCheckAsyncEvent 更新后实现完整的事件数据处理
    }
}
