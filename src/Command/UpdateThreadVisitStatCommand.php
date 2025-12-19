<?php

namespace ForumBundle\Command;

use ForumBundle\Entity\Thread;
use ForumBundle\Repository\ThreadRepository;
use ForumBundle\Service\ThreadStatisticsFacade;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: self::NAME, description: '统计帖子的指标')]
final class UpdateThreadVisitStatCommand extends Command
{
    public const NAME = 'forum:update-thread-visit-stat';

    public function __construct(
        private readonly ThreadRepository $threadRepository,
        private readonly ThreadStatisticsFacade $threadStatisticsFacade,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var list<Thread> $threads */
        $threads = $this->threadRepository->createQueryBuilder('t')
            ->leftJoin('t.visitStat', 'vs')
            ->where('vs.id IS NULL')
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->setMaxResults(500)
            ->getResult()
        ;
        foreach ($threads as $thread) {
            try {
                $this->threadStatisticsFacade->updateAllStat($thread);
            } catch (\Throwable $throwable) {
                $output->writeln("同步帖子指标[{$thread->getId()}]发生异常：" . $throwable);
            }
        }

        return Command::SUCCESS;
    }
}
