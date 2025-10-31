<?php

namespace ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadLike;
use ForumBundle\Enum\MessageType;
use ForumBundle\Event\AfterLikeThread;
use ForumBundle\Repository\MessageNotificationRepository;
use ForumBundle\Repository\ThreadLikeRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ThreadLikeService
{
    public function __construct(
        private NotifyService $notifyService,
        private StatService $statService,
        private ThreadLikeRepository $threadLikeRepository,
        private MessageNotificationRepository $messageNotificationRepository,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function like(ThreadLike $like): array
    {
        $thread = $like->getThread();
        $existingLike = $this->findExistingThreadLike($like);
        $isFirstTime = null === $existingLike;

        $threadLike = $this->processThreadLike($like, $existingLike);
        $this->persistThreadLike($threadLike);

        $result = $this->buildLikeResult($threadLike);
        $this->updateLikeStat($thread);

        if ($isFirstTime) {
            $result = $this->addRewardToResult($result, $threadLike);
        }

        $this->handleLikeNotification($like, $threadLike);

        return $result;
    }

    private function findExistingThreadLike(ThreadLike $like): ?ThreadLike
    {
        return $this->threadLikeRepository->findOneBy([
            'user' => $like->getUser(),
            'thread' => $like->getThread(),
        ]);
    }

    private function processThreadLike(ThreadLike $like, ?ThreadLike $existingLike): ThreadLike
    {
        if (null === $existingLike) {
            return $like;
        }

        $currentStatus = $existingLike->getStatus();
        $existingLike->setStatus(null !== $currentStatus ? abs($currentStatus - 1) : 1);

        return $existingLike;
    }

    private function persistThreadLike(ThreadLike $threadLike): void
    {
        $this->entityManager->persist($threadLike);
        $this->entityManager->flush();
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLikeResult(ThreadLike $threadLike): array
    {
        return [
            'message' => 1 === $threadLike->getStatus() ? '点赞成功' : '取消点赞成功',
        ];
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return array<string, mixed>
     */
    private function addRewardToResult(array $result, ThreadLike $threadLike): array
    {
        $event = new AfterLikeThread();
        $event->setThreadLike($threadLike);
        $event->setResult(['id' => $threadLike->getId()]);
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

    private function handleLikeNotification(ThreadLike $like, ThreadLike $threadLike): void
    {
        $likeUser = $like->getUser();
        $threadUser = $like->getThread()->getUser();

        if (!$this->shouldSendNotification($likeUser, $threadUser)) {
            return;
        }

        $this->processLikeMessage($like, $threadLike);
    }

    private function shouldSendNotification(?UserInterface $likeUser, ?UserInterface $threadUser): bool
    {
        if (null === $likeUser || null === $threadUser) {
            return false;
        }

        $likeUserId = method_exists($likeUser, 'getId') ? $likeUser->getId() : $likeUser->getUserIdentifier();
        $threadUserId = method_exists($threadUser, 'getId') ? $threadUser->getId() : $threadUser->getUserIdentifier();

        return $likeUserId !== $threadUserId;
    }

    private function processLikeMessage(ThreadLike $like, ThreadLike $threadLike): void
    {
        $message = $this->findExistingLikeMessage($like, $threadLike);

        if (1 !== $threadLike->getStatus()) {
            $this->handleUnlikeMessage($message);
        } else {
            $this->handleLikeMessage($like, $threadLike, $message);
        }
    }

    private function findExistingLikeMessage(ThreadLike $like, ThreadLike $threadLike): ?MessageNotification
    {
        return $this->messageNotificationRepository->findOneBy([
            'user' => $like->getUser(),
            'type' => MessageType::LIKE_THREAD,
            'targetId' => $threadLike->getId(),
        ]);
    }

    private function handleUnlikeMessage(?MessageNotification $message): void
    {
        if (null !== $message) {
            $message->setDeleted(1);
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }
    }

    private function handleLikeMessage(ThreadLike $like, ThreadLike $threadLike, ?MessageNotification $message): void
    {
        if (null === $message) {
            $message = $this->createLikeMessage($like, $threadLike);
        }

        $message->setReadStatus(0);
        $message->setDeleted(0);
        $this->notifyService->send($message);
    }

    private function createLikeMessage(ThreadLike $like, ThreadLike $threadLike): MessageNotification
    {
        $message = new MessageNotification();
        $message->setUser($like->getThread()->getUser());
        $message->setType(MessageType::LIKE_THREAD);
        $message->setSender($like->getUser());
        $message->setContent('');
        $targetId = $threadLike->getId();
        if (null !== $targetId) {
            $message->setTargetId($targetId);
        }
        $message->setReadStatus(0);

        return $message;
    }

    public function updateLikeStat(Thread $thread): void
    {
        $this->statService->updateLikeStatistics($thread);
    }
}
