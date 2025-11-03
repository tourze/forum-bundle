<?php

namespace ForumBundle\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Event\AfterGetThreadDetailEvent;
use ForumBundle\Repository\ThreadCollectRepository;
use ForumBundle\Repository\ThreadLikeRepository;
use ForumBundle\Vo\ThreadDetail;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\UserFollowBundle\Service\FollowService;

readonly class ThreadDetailBuilder
{
    public function __construct(
        private FollowService $followService,
        private ThreadLikeRepository $threadLikeRepository,
        private ThreadCollectRepository $threadCollectRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function buildThreadDetail(Thread $thread, ?UserInterface $currentUser): ThreadDetail
    {
        $threadDetail = new ThreadDetail();

        $this->setCatalogInfo($threadDetail, $thread);
        $this->setFollowInfo($threadDetail, $thread, $currentUser);
        $this->setMediaInfo($threadDetail, $thread);
        $this->setStatisticsInfo($threadDetail, $thread);
        $this->setBasicThreadInfo($threadDetail, $thread);
        $this->setUserInteractionInfo($threadDetail, $thread, $currentUser);
        $this->setUserInfo($threadDetail, $thread);
        $this->dispatchThreadDetailEvent($threadDetail, $thread);

        return $threadDetail;
    }

    private function setCatalogInfo(ThreadDetail $threadDetail, Thread $thread): void
    {
        $catalog = $thread->getCatalog();
        if (null !== $catalog) {
            $catalogId = $catalog->getId();
            $catalogName = $catalog->getName();
            if (null !== $catalogId) {
                $threadDetail->setTopicId($catalogId);
            }
            $threadDetail->setTopicName($catalogName);
        }
    }

    private function setFollowInfo(ThreadDetail $threadDetail, Thread $thread, ?UserInterface $currentUser): void
    {
        if (null === $currentUser) {
            return;
        }

        $user = $thread->getUser();
        if (null === $user) {
            return;
        }

        $isFollowing = $this->followService->isFollowing($currentUser, $user);
        $threadDetail->setFollow($isFollowing);
    }

    private function setMediaInfo(ThreadDetail $threadDetail, Thread $thread): void
    {
        $threadDetail->setCoverPicture($thread->getCoverPicture());

        $mediaFiles = $thread->getThreadMedia();
        if (0 === $mediaFiles->count()) {
            return;
        }

        if ('' === $threadDetail->getCoverPicture() && isset($mediaFiles[0])) {
            $threadDetail->setCoverPicture($mediaFiles[0]->getThumbnail() ?? '');
        }

        $files = [];
        foreach ($mediaFiles as $mediaFile) {
            $files[] = [
                'type' => $mediaFile->getType(),
                'path' => $mediaFile->getPath(),
                'thumbnail' => $mediaFile->getThumbnail(),
            ];
        }

        $threadDetail->setMediaFiles($files);
    }

    private function setStatisticsInfo(ThreadDetail $threadDetail, Thread $thread): void
    {
        $visitStat = $thread->getVisitStat();

        $threadDetail->setLikeCount($visitStat?->getLikeTotal() ?? 0);
        $threadDetail->setCollectCount($visitStat?->getCollectCount() ?? 0);
        $threadDetail->setShareCount($visitStat?->getShareTotal() ?? 0);
        $threadDetail->setCommentCount($visitStat?->getCommentTotal() ?? 0);
    }

    private function setBasicThreadInfo(ThreadDetail $threadDetail, Thread $thread): void
    {
        $status = $thread->getStatus();
        $threadDetail->setStatus(null !== $status ? $status->value : '');
        $threadDetail->setThreadId($thread->getId() ?? '');
        $threadDetail->setReleaseTime($thread->getCreateTime()?->format('Y-m-d H:i:s') ?? '');
        $threadDetail->setTitle($thread->getTitle() ?? '');
        $threadDetail->setContent($thread->getContent() ?? '');
        $threadDetail->setTop($thread->isTop() ?? false);
        $closeComment = $thread->isCloseComment();
        $threadDetail->setCloseComment($closeComment ?? false);
        $hot = $thread->isHot();
        $threadDetail->setHot($hot ?? false);
        $threadDetail->setRejectReason($thread->getRejectReason());
    }

    private function setUserInteractionInfo(ThreadDetail $threadDetail, Thread $thread, ?UserInterface $currentUser): void
    {
        if (null === $currentUser) {
            return;
        }

        $isLike = $this->threadLikeRepository->findOneBy([
            'thread' => $thread,
            'status' => 1,
            'user' => $currentUser,
        ]);

        $isCollect = $this->threadCollectRepository->findOneBy([
            'thread' => $thread,
            'valid' => true,
            'user' => $currentUser,
        ]);

        $threadDetail->setLike(null !== $isLike);
        $threadDetail->setCollect(null !== $isCollect);

        $this->setOwnershipInfo($threadDetail, $thread, $currentUser);
    }

    private function setOwnershipInfo(ThreadDetail $threadDetail, Thread $thread, UserInterface $currentUser): void
    {
        $user = $thread->getUser();
        if (null === $user) {
            return;
        }

        $userId = $this->getUserId($user);
        $currentUserId = $this->getUserId($currentUser);

        $threadDetail->setMine($userId === $currentUserId);
    }

    private function getUserId(?UserInterface $user): string
    {
        if (null === $user) {
            return '';
        }

        if (method_exists($user, 'getId')) {
            $id = $user->getId();
            if (is_string($id)) {
                return $id;
            }
            if (is_int($id)) {
                return (string) $id;
            }

            return '';
        }

        return $user->getUserIdentifier();
    }

    private function setUserInfo(ThreadDetail $threadDetail, Thread $thread): void
    {
        $user = $thread->getUser();
        if (null === $user) {
            return;
        }

        $userId = $this->getUserId($user);

        $threadDetail->setUserAvatar($this->getUserAvatar($user));
        $threadDetail->setUserId(strval($userId));
        $threadDetail->setUserName($this->getUserName($user));

        $this->setOfficialStatus($threadDetail, $user, $userId);
    }

    private function getUserAvatar(?UserInterface $user): string
    {
        if (null === $user || !method_exists($user, 'getAvatar')) {
            return '';
        }

        $avatar = $user->getAvatar();

        return is_string($avatar) ? $avatar : '';
    }

    private function getUserName(?UserInterface $user): string
    {
        if (null === $user || !method_exists($user, 'getNickName')) {
            return '';
        }

        $nickname = $user->getNickName();

        return is_string($nickname) ? $nickname : '';
    }

    private function setOfficialStatus(ThreadDetail $threadDetail, UserInterface $user, string $userId): void
    {
        if ('1' === $userId) {
            $officialNickname = $_ENV['FORUM_OFFICIAL_NICKNAME'] ?? '官方账号';
            // 确保是字符串类型
            if (is_string($officialNickname)) {
                $threadDetail->setUserName($officialNickname);
            }
            $threadDetail->setOfficial(true);
        } elseif (method_exists($user, 'getType') && 'admin' === $user->getType()) {
            $threadDetail->setOfficial(true);
        }
    }

    private function dispatchThreadDetailEvent(ThreadDetail $threadDetail, Thread $thread): void
    {
        $event = new AfterGetThreadDetailEvent();
        $event->setThread($thread);
        $this->eventDispatcher->dispatch($event);
        $threadDetail->setExtraInfo($event->getExtraInfo());
    }
}
