<?php

namespace ForumBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\VisitStat;
use ForumBundle\Repository\VisitStatRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '* */1 * * *')]
#[AsCommand(name: self::NAME, description: '计算帖子访问统计指标排名')]
class UpdateThreadVisitStatVisitRankCommand extends LockableCommand
{
    public const NAME = 'forum:update-thread-stat-visit-rank';

    public function __construct(
        private readonly VisitStatRepository $visitStatRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $bool = filter_var($_ENV['ENABLE_THREAD_STAT_RANK_TASK'] ?? null, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
        $num = filter_var(
            $_ENV['THREAD_RANK_LIMIT'] ?? null,
            FILTER_VALIDATE_INT,
            ['options' => ['default' => 50, 'min_range' => 1]]
        );
        if (!$bool) {
            return Command::SUCCESS;
        }
        /** @var list<VisitStat> $likeTotals */
        $likeTotals = $this->visitStatRepository->createQueryBuilder('a')
            ->orderBy('a.visitTotal', 'DESC')
            ->setMaxResults($num)
            ->getQuery()
            ->getResult()
        ;
        $ids = [];
        foreach ($likeTotals as $key => $likeTotal) {
            $likeTotal->setVisitRank($key + 1);
            $this->entityManager->persist($likeTotal);
            $this->entityManager->flush();
            $ids[] = $likeTotal->getId();
            $this->entityManager->detach($likeTotal);
        }

        /** @var list<VisitStat> $likeTotals */
        $likeTotals = $this->visitStatRepository->createQueryBuilder('a')
            ->where('a.visitRank > 0')
            ->andWhere('a.id NOT IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult()
        ;
        foreach ($likeTotals as $likeTotal) {
            $likeTotal->setVisitRank(0);
            $this->entityManager->persist($likeTotal);
            $this->entityManager->flush();
            $this->entityManager->detach($likeTotal);
        }

        return Command::SUCCESS;
    }
}
