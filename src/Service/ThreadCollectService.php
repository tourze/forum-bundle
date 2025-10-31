<?php

namespace ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCollect;
use ForumBundle\Enum\MessageType;
use ForumBundle\Event\AfterCollectThread;
use ForumBundle\Repository\MessageNotificationRepository;
use ForumBundle\Repository\ThreadCollectRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ThreadCollectService
{
    public function __construct(
        private NotifyService $notifyService,
        private StatService $statService,
        private ThreadCollectRepository $threadCollectRepository,
        private MessageNotificationRepository $messageNotificationRepository,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function collect(ThreadCollect $collect): array
    {
        $thread = $collect->getThread();
        $existingCollect = $this->findExistingThreadCollect($collect);
        $isFirstTime = null === $existingCollect;

        $threadCollect = $this->processThreadCollect($collect, $existingCollect);
        $this->persistThreadCollect($threadCollect);

        $result = $this->buildCollectResult($threadCollect);
        if (null !== $thread) {
            $this->updateCollectStat($thread);
        }

        if ($isFirstTime) {
            $result = $this->addCollectRewardToResult($result, $threadCollect);
        }

        $this->handleCollectNotification($collect, $threadCollect);

        return $result;
    }

    private function findExistingThreadCollect(ThreadCollect $collect): ?ThreadCollect
    {
        return $this->threadCollectRepository->findOneBy([
            'user' => $collect->getUser(),
            'thread' => $collect->getThread(),
        ]);
    }

    private function processThreadCollect(ThreadCollect $collect, ?ThreadCollect $existingCollect): ThreadCollect
    {
        if (null === $existingCollect) {
            return $collect;
        }

        $currentValid = $existingCollect->isValid();
        $existingCollect->setValid(null !== $currentValid ? !$currentValid : true);

        return $existingCollect;
    }

    private function persistThreadCollect(ThreadCollect $threadCollect): void
    {
        $this->entityManager->persist($threadCollect);
        $this->entityManager->flush();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCollectResult(ThreadCollect $threadCollect): array
    {
        return [
            'message' => true === $threadCollect->isValid() ? '收藏成功' : '取消收藏成功',
        ];
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return array<string, mixed>
     */
    private function addCollectRewardToResult(array $result, ThreadCollect $threadCollect): array
    {
        $event = new AfterCollectThread();
        $event->setThreadCollect($threadCollect);
        $event->setResult(['id' => $threadCollect->getId()]);
        $this->eventDispatcher->dispatch($event);

        $eventResult = $event->getResult();
        if (isset($eventResult['amount']) && '' !== $eventResult['amount'] && 0 !== $eventResult['amount']) {
            $result['ziwiReward'] = [
                'type' => 'z_credit',
                'amount' => $eventResult['amount'],
            ];
        }

        return $result;
    }

    private function handleCollectNotification(ThreadCollect $collect, ThreadCollect $threadCollect): void
    {
        $collectUser = $collect->getUser();
        $thread = $collect->getThread();
        $threadUser = null !== $thread ? $thread->getUser() : null;

        if (!$this->shouldSendNotification($collectUser, $threadUser)) {
            return;
        }

        $this->processCollectMessage($collect, $threadCollect);
    }

    private function shouldSendNotification(?UserInterface $collectUser, ?UserInterface $threadUser): bool
    {
        if (null === $collectUser || null === $threadUser) {
            return false;
        }

        $collectUserId = method_exists($collectUser, 'getId') ? $collectUser->getId() : $collectUser->getUserIdentifier();
        $threadUserId = method_exists($threadUser, 'getId') ? $threadUser->getId() : $threadUser->getUserIdentifier();

        return $collectUserId !== $threadUserId;
    }

    private function processCollectMessage(ThreadCollect $collect, ThreadCollect $threadCollect): void
    {
        $message = $this->findExistingCollectMessage($collect, $threadCollect);

        if (true !== $threadCollect->isValid()) {
            $this->handleUncollectMessage($message);
        } else {
            $this->handleCollectMessage($collect, $threadCollect, $message);
        }
    }

    private function findExistingCollectMessage(ThreadCollect $collect, ThreadCollect $threadCollect): ?MessageNotification
    {
        return $this->messageNotificationRepository->findOneBy([
            'user' => $collect->getUser(),
            'type' => MessageType::COLLECT_THREAD,
            'targetId' => $threadCollect->getId(),
        ]);
    }

    private function handleUncollectMessage(?MessageNotification $message): void
    {
        if (null !== $message) {
            $message->setDeleted(1);
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }
    }

    private function handleCollectMessage(ThreadCollect $collect, ThreadCollect $threadCollect, ?MessageNotification $message): void
    {
        if (null === $message) {
            $message = $this->createCollectMessage($collect, $threadCollect);
        }

        $message->setReadStatus(0);
        $message->setDeleted(0);
        $this->notifyService->send($message);
    }

    private function createCollectMessage(ThreadCollect $collect, ThreadCollect $threadCollect): MessageNotification
    {
        $message = new MessageNotification();
        $thread = $collect->getThread();
        if (null !== $thread) {
            $message->setUser($thread->getUser());
        }
        $message->setType(MessageType::COLLECT_THREAD);
        $message->setSender($collect->getUser());
        $message->setContent('');
        $targetId = $threadCollect->getId();
        if (null !== $targetId) {
            $message->setTargetId($targetId);
        }
        $message->setReadStatus(0);

        return $message;
    }

    public function updateCollectStat(Thread $thread): void
    {
        $this->statService->updateCollectStatistics($thread);
    }
}
