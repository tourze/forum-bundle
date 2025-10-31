<?php

namespace ForumBundle\Service;

use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadDimension;
use ForumBundle\Repository\ThreadDimensionRepository;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EcolBundle\Service\Engine;
use Yiisoft\Arrays\ArrayHelper;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'forum')]
readonly class DimensionService
{
    public function __construct(
        private ThreadDimensionRepository $dimensionRepository,
        private Engine $engine,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 计算帖子在指定维度下的数值
     *
     * @return array<int, array{id: mixed, value: int}>
     */
    public function calcThreadDimension(Thread $thread, Dimension $dimension): array
    {
        /** @var array<int, array{id: mixed, value: int}> $result */
        $result = [];

        foreach ($dimension->getSortingRules() as $rule) {
            if (null === $rule->getFormula() || '' === $rule->getFormula()) {
                continue;
            }

            /** @var bool|float|int|string|null $tmpValue */
            $tmpValue = 0;
            try {
                $evalResult = $this->engine->evaluate($rule->getFormula(), [
                    'thread' => $thread,
                    'now' => CarbonImmutable::now(),
                ]);
                // 确保结果是可转换为 int 的类型
                if (is_bool($evalResult) || is_float($evalResult) || is_int($evalResult) || is_string($evalResult)) {
                    $tmpValue = $evalResult;
                } elseif (null === $evalResult) {
                    $tmpValue = 0;
                }
            } catch (\Throwable $exception) {
                $this->logger->error('计算帖子在指定维度下的数值时发生异常', [
                    'exception' => $exception,
                    'thread' => $thread,
                    'rule' => $rule,
                ]);
                $tmpValue = 0;
            }
            $tmp = intval($tmpValue);
            $result[] = [
                'id' => $rule->getId(),
                'value' => $tmp,
            ];
        }

        $threadDimension = $this->dimensionRepository->findOneBy([
            'thread' => $thread,
            'dimension' => $dimension,
        ]);
        if (null === $threadDimension) {
            $threadDimension = new ThreadDimension();
            $threadDimension->setThread($thread);
            $threadDimension->setDimension($dimension);
        }

        $threadDimension->setValue(array_sum(ArrayHelper::getColumn($result, 'value')));
        if ($threadDimension->getValue() < 0) {
            $threadDimension->setValue(0);
        }

        /** @var array<string, mixed> $contextData */
        $contextData = ['items' => $result];
        $threadDimension->setContext($contextData);
        $this->entityManager->persist($threadDimension);
        $this->entityManager->flush();

        return $result;
    }
}
