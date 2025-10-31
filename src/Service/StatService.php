<?php

namespace ForumBundle\Service;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\VisitStat;
use ForumBundle\Enum\ThreadCommentState;
use ForumBundle\Repository\ForumShareRecordRepository;
use ForumBundle\Repository\ThreadCollectRepository;
use ForumBundle\Repository\ThreadCommentRepository;
use ForumBundle\Repository\ThreadLikeRepository;
use ForumBundle\Repository\VisitStatRepository;
use Tourze\DoctrineEntityLockBundle\Service\EntityLockService;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

class StatService
{
    public function __construct(
        private ThreadCommentRepository $threadCommentRepository,
        private ThreadCollectRepository $threadCollectRepository,
        private ThreadLikeRepository $threadLikeRepository,
        private ForumShareRecordRepository $forumShareRecordRepository,
        private VisitStatRepository $visitStatRepository,
        private EntityLockService $entityLockService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Async]
    public function asyncUpdateCommentTotal(VisitStat $stat): void
    {
        $this->updateCommentTotal($stat);
    }

    public function updateCommentTotal(VisitStat $stat): void
    {
        $commentTotal = $this->threadCommentRepository->count([
            'thread' => $stat->getThread(),
            'status' => ThreadCommentState::AUDIT_PASS,
        ]);
        $stat->setCommentTotal($commentTotal);
        $stat->setUpdateTime(CarbonImmutable::now());
        $this->entityManager->persist($stat);
        $this->entityManager->flush();
    }

    #[Async]
    public function asyncUpdateCollectCount(VisitStat $stat): void
    {
        $this->updateCollectCount($stat);
    }

    public function updateCollectCount(VisitStat $stat): void
    {
        $threadCollectCount = $this->threadCollectRepository->count([
            'thread' => $stat->getThread(),
            'valid' => true,
        ]);
        $stat->setCollectCount($threadCollectCount);
        $stat->setUpdateTime(CarbonImmutable::now());
        $this->entityManager->persist($stat);
        $this->entityManager->flush();
    }

    /**
     * 更新帖子所有统计信息
     */
    public function updateAllStatistics(Thread $thread): void
    {
        $visitStat = $this->findOrCreateVisitStat($thread);

        $commentTotal = $thread->getThreadComments()->count();
        $threadLikeCount = $this->threadLikeRepository->count([
            'thread' => $thread,
            'status' => 1,
        ]);
        $threadShareCount = $this->forumShareRecordRepository->count([
            'type' => 'thread',
            'sourceId' => $thread->getId(),
        ]);
        $threadCollectCount = $this->threadCollectRepository->count([
            'thread' => $thread,
            'valid' => true,
        ]);

        $visitStat->setVisitTotal($visitStat->getVisitTotal() + 1);
        $visitStat->setCommentTotal($commentTotal ?? 0);
        $visitStat->setCollectCount($threadCollectCount);
        $visitStat->setLikeTotal($threadLikeCount);
        $visitStat->setShareTotal($threadShareCount);
        $visitStat->setUpdateTime(CarbonImmutable::now());

        $this->entityManager->persist($visitStat);
        $this->entityManager->flush();
    }

    /**
     * 更新点赞统计
     */
    public function updateLikeStatistics(Thread $thread): void
    {
        $visitStat = $this->findOrCreateVisitStat($thread);

        $threadLikeCount = $this->threadLikeRepository->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.thread = :thread')
            ->andWhere('l.status = :status')
            ->setParameter('thread', $thread)
            ->setParameter('status', 1)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        if (is_numeric($threadLikeCount)) {
            $visitStat->setLikeTotal((int) $threadLikeCount);
        } else {
            $visitStat->setLikeTotal(0);
        }
        $this->saveVisitStat($visitStat);
    }

    /**
     * 更新收藏统计
     */
    public function updateCollectStatistics(Thread $thread): void
    {
        $visitStat = $this->findOrCreateVisitStat($thread);

        if ($visitStat->getCommentTotal() > ($_ENV['FORUM_UPDATE_COLLECT_STAT_ASYNC_REACH'] ?? 10000)) {
            $this->asyncUpdateCollectCount($visitStat);
        } else {
            $this->updateCollectCount($visitStat);
        }
    }

    /**
     * 更新分享统计
     */
    public function updateShareStatistics(Thread $thread): void
    {
        $this->entityLockService->lockEntity($thread, function () use ($thread): void {
            $visitStat = $this->findOrCreateVisitStat($thread);

            $threadShareCount = $this->forumShareRecordRepository->count([
                'type' => 'thread',
                'sourceId' => $thread->getId(),
            ]);

            $visitStat->setShareTotal($threadShareCount);
            $this->saveVisitStat($visitStat);
        });
    }

    /**
     * 更新评论统计
     */
    public function updateCommentStatistics(Thread $thread): void
    {
        $visitStat = $this->findOrCreateVisitStat($thread);

        if ($visitStat->getCommentTotal() > ($_ENV['FORUM_UPDATE_COMMENT_STAT_ASYNC_REACH'] ?? 10000)) {
            $this->asyncUpdateCommentTotal($visitStat);
        } else {
            $this->updateCommentTotal($visitStat);
        }
    }

    /**
     * 更新访问统计
     */
    public function updateVisitStatistics(Thread $thread): void
    {
        $visitStat = $this->findOrCreateVisitStat($thread);
        $visitStat->setVisitTotal($visitStat->getVisitTotal() + 1);
        $this->saveVisitStat($visitStat);
    }

    private function findOrCreateVisitStat(Thread $thread): VisitStat
    {
        $visitStat = $this->visitStatRepository->findOneBy(['thread' => $thread]);

        if (null === $visitStat) {
            $visitStat = new VisitStat();
            $visitStat->setThread($thread);
            $visitStat->setCreateTime(CarbonImmutable::now());
            $visitStat->setVisitTotal(0);
            $visitStat->setCommentTotal(0);
        }

        return $visitStat;
    }

    private function saveVisitStat(VisitStat $visitStat): void
    {
        $visitStat->setUpdateTime(CarbonImmutable::now());
        $this->entityManager->persist($visitStat);
        $this->entityManager->flush();
    }
}
