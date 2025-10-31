<?php

namespace ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Event\AfterPublishThread;
use ForumBundle\Repository\ThreadRepository;
use ForumBundle\Vo\ThreadDetail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

/**
 * 帖子管理服务 - 专注于核心的帖子创建和详情获取功能
 */
class ThreadManagementService
{
    public function __construct(
        private ThreadDetailBuilder $threadDetailBuilder,
        private ThreadRepository $threadRepository,
        private Security $security,
        private EventDispatcherInterface $eventDispatcher,
        private EntityManagerInterface $entityManager,
        private StatService $statService,
    ) {
    }

    /**
     * 新增帖子
     *
     * @return array<string, mixed>
     */
    public function add(Thread $thread): array
    {
        $this->entityManager->persist($thread);
        $this->entityManager->flush();

        try {
            $this->statService->updateAllStatistics($thread);
        } catch (\Throwable $throwable) {
        }

        $event = new AfterPublishThread();
        $event->setRecord($thread);
        $event->setResult(['id' => $thread->getId()]);
        $this->eventDispatcher->dispatch($event);
        $tmp = $event->getResult();

        $rs = [
            'id' => $thread->getId(),
        ];
        if (isset($tmp['amount']) && '' !== $tmp['amount']) {
            $rs['ziwiReward'] = [
                'type' => 'z_credit',
                'amount' => $tmp['amount'],
            ];
        }

        return $rs;
    }

    /**
     * 获取帖子详情
     * $isGetDetail 获取详情接口调用
     */
    public function getDetail(string $threadId, bool $isGetDetail = false): ThreadDetail
    {
        $thread = $this->findThreadById($threadId);
        $currentUser = $this->security->getUser();

        $threadDetail = $this->threadDetailBuilder->buildThreadDetail($thread, $currentUser);

        if ($isGetDetail) {
            $threadId = $thread->getId();
            if (null !== $threadId) {
                $this->threadVisitStat($threadId, 'visit');
            }
        }

        return $threadDetail;
    }

    public function findThreadById(string $threadId): Thread
    {
        $thread = $this->threadRepository->findOneBy(['id' => $threadId]);
        if (null === $thread) {
            throw new \RuntimeException('帖子不存在~');
        }

        return $thread;
    }

    #[Async]
    public function threadVisitStat(string $threadId, string $type): void
    {
        $thread = $this->threadRepository->findOneBy(['id' => $threadId]);
        if (null === $thread) {
            return;
        }

        match ($type) {
            'like' => $this->statService->updateLikeStatistics($thread),
            'share' => $this->statService->updateShareStatistics($thread),
            'comment' => $this->statService->updateCommentStatistics($thread),
            'visit' => $this->statService->updateVisitStatistics($thread),
            'collect' => $this->statService->updateCollectStatistics($thread),
            default => null,
        };
    }
}
