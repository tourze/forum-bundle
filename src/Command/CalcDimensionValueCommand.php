<?php

namespace ForumBundle\Command;

use ForumBundle\Enum\ThreadState;
use ForumBundle\Message\CalcDimensionValueMessage;
use ForumBundle\Repository\DimensionRepository;
use ForumBundle\Repository\ThreadRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Tourze\LockCommandBundle\Command\LockableCommand;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

#[AsCronTask(expression: '30 * * * *')]
#[AsCommand(name: self::NAME, description: '计算帖子的维度得分')]
#[WithMonologChannel(channel: 'forum')]
class CalcDimensionValueCommand extends LockableCommand
{
    public const NAME = 'forum:calc-dimension-value';

    public function __construct(
        private readonly DimensionRepository $dimensionRepository,
        private readonly ThreadRepository $threadRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dimensions = $this->dimensionRepository->findBy(['valid' => true]);
        if ([] === $dimensions) {
            return Command::FAILURE;
        }

        $currentThread = null;
        try {
            /** @var list<array{id: string}> $threads */
            $threads = $this->threadRepository->createQueryBuilder('a')
                ->select('a.id')
                ->where('a.status = :status')
                ->setParameter('status', ThreadState::AUDIT_PASS)
                ->orderBy('a.id', 'DESC')
                ->getQuery()
                ->getArrayResult()
            ;
            foreach ($threads as $thread) {
                if (!isset($thread['id']) || !is_string($thread['id'])) {
                    continue;
                }
                $currentThread = $thread;
                $message = new CalcDimensionValueMessage();
                $message->setThreadId($thread['id']);
                $this->messageBus->dispatch($message);
            }
        } catch (\Throwable $exception) {
            $this->logger->error('计算帖子的维度得分时发生异常', [
                'exception' => $exception,
                'thread' => $currentThread,
            ]);
        }

        return Command::SUCCESS;
    }
}
