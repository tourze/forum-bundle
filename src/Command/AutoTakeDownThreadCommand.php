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
#[AsCommand(name: self::NAME, description: '自动下架帖子')]
class AutoTakeDownThreadCommand extends LockableCommand
{
    public const NAME = 'forum:auto-take-down-thread';

    public function __construct(
        private readonly ThreadRepository $threadRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = CarbonImmutable::now()->format('Y-m-d H:i');
        $query = $this->threadRepository->createQueryBuilder('t')
            ->where('t.status=:status')
            ->andWhere('t.autoTakeDownTime <= :autoTakeDownTime')
            ->andWhere('t.official = :official')
            ->setParameter('status', ThreadState::AUDIT_PASS)
            ->setParameter('official', true)
            ->setParameter('autoTakeDownTime', $now)
            ->getQuery()
            ->toIterable()
        ;

        /** @var Thread $thread */
        foreach ($query as $thread) {
            $thread->setStatus(ThreadState::AUDIT_REJECT);
            $this->entityManager->persist($thread);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
