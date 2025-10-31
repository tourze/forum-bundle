<?php

namespace ForumBundle\Command;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Thread;
use ForumBundle\Enum\ThreadState;
use ForumBundle\Repository\ThreadRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '* * * * *')]
#[AsCommand(name: self::NAME, description: '自动上架帖子')]
class AutoReleaseThreadCommand extends LockableCommand
{
    public const NAME = 'forum:auto-release-thread';

    public function __construct(
        private readonly ThreadRepository $threadRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = CarbonImmutable::now();
        $query = $this->threadRepository->createQueryBuilder('t')
            ->where('t.status=:status')
            ->andWhere('t.autoReleaseTime <= :autoReleaseTime')
            ->andWhere('t.official = :official')
            ->setParameter('status', ThreadState::AUDIT_REJECT)
            ->setParameter('official', true)
            ->setParameter('autoReleaseTime', $now)
            ->getQuery()
            ->toIterable()
        ;

        /** @var Thread $thread */
        foreach ($query as $thread) {
            // 过了自动下架时间的不要再上架
            if (null !== $thread->getAutoTakeDownTime() && $now->greaterThan($thread->getAutoTakeDownTime())) {
                continue;
            }
            $thread->setStatus(ThreadState::AUDIT_PASS);
            $this->entityManager->persist($thread);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
