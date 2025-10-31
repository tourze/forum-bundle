<?php

namespace ForumBundle\MessageHandler;

use ForumBundle\Message\CalcDimensionValueMessage;
use ForumBundle\Repository\DimensionRepository;
use ForumBundle\Repository\ThreadRepository;
use ForumBundle\Service\DimensionService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
#[WithMonologChannel(channel: 'forum')]
readonly class CalcDimensionValueHandler
{
    public function __construct(
        private ThreadRepository $threadRepository,
        private DimensionRepository $dimensionRepository,
        private DimensionService $dimensionService,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CalcDimensionValueMessage $message): void
    {
        $thread = $this->threadRepository->findOneBy([
            'id' => $message->getThreadId(),
        ]);
        if (null === $thread) {
            return;
        }

        $dimensions = $this->dimensionRepository->findBy(['valid' => true]);
        if ([] === $dimensions) {
            return;
        }

        foreach ($dimensions as $dimension) {
            try {
                $this->dimensionService->calcThreadDimension($thread, $dimension);
            } catch (\Throwable $throwable) {
                $this->logger->error('处理帖子维度异常', [
                    'threadId' => $thread->getId(),
                    'throwable' => $throwable,
                ]);
            }
        }
    }
}
