<?php

namespace ForumBundle\Service;

use ForumBundle\Enum\MessageType;
use ForumBundle\Repository\MessageNotificationRepository;
use ForumBundle\Repository\ThreadCommentLikeRepository;
use ForumBundle\Repository\ThreadLikeRepository;
use ForumBundle\Vo\UserInfo;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserFollowBundle\Service\FollowService;

readonly class UserService
{
    public function __construct(
        private ThreadCommentLikeRepository $threadCommentLikeRepository,
        private ThreadLikeRepository $threadLikeRepository,
        private FollowService $followService,
        private MessageNotificationRepository $messageNotificationRepository,
    ) {
    }

    public function getUserInfo(UserInterface $user): UserInfo
    {
        $userInfo = new UserInfo();
        $userInfo->setAvatar($this->extractUserAvatar($user));
        $userInfo->setNickname($this->extractUserNickname($user));
        $userInfo->setUserId($this->extractUserId($user));

        // 评论点赞数
        $threadCommentLikeCount = $this->threadCommentLikeRepository->createQueryBuilder('l')
            ->select('count(1)')
            ->leftJoin('l.threadComment', 't')
            ->where('l.status=:status')
            ->setParameter('status', 1)
            ->andWhere('t.user=:user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
        // 帖子点赞数
        $threadLikeCount = $this->threadLikeRepository->createQueryBuilder('l')
            ->select('count(1)')
            ->leftJoin('l.thread', 't')
            ->where('l.status =:status')
            ->setParameter('status', 1)
            ->andWhere('t.user=:user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult()
        ;
        $fansTotal = $this->followService->getFansCount($user);
        $followTotal = $this->followService->getFollowCount($user);
        $messageTotal = $this->messageNotificationRepository->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.user=:user')
            ->andWhere('m.readStatus = :readStatus')
            ->andWhere('m.deleted = :deleted')
            ->andWhere('m.type IN (:type)')
            ->setParameter('user', $user)
            ->setParameter('readStatus', 0)
            ->setParameter('deleted', 0)
            ->setParameter('type', [MessageType::SYSTEM_NOTIFICATION, MessageType::REPLY, MessageType::FOLLOW])
            ->getQuery()
            ->getSingleScalarResult()
        ;
        // 勋章总数（暂未实现徽章系统）
        $medals = [];
        // 获赞总数
        $userInfo->setLikeCount(intval($threadCommentLikeCount) + intval($threadLikeCount));
        $userInfo->setFansTotal($fansTotal); // 粉丝数量
        $userInfo->setFollowTotal(max(0, $followTotal - 1)); // 关注总数 减去关注官方的数量
        $userInfo->setMessageTotal(intval($messageTotal)); // 未读消息总数
        $userInfo->setMedalTotal(count($medals)); // 徽章数

        return $userInfo;
    }

    private function extractUserAvatar(UserInterface $user): string
    {
        if (!method_exists($user, 'getAvatar')) {
            return '';
        }
        $avatarValue = $user->getAvatar();
        return is_string($avatarValue) ? $avatarValue : '';
    }

    private function extractUserNickname(UserInterface $user): string
    {
        if (!method_exists($user, 'getNickName')) {
            return '';
        }
        $nicknameValue = $user->getNickName();
        return is_string($nicknameValue) ? $nicknameValue : '';
    }

    private function extractUserId(UserInterface $user): int
    {
        if (method_exists($user, 'getId')) {
            $idValue = $user->getId();
            if (is_int($idValue)) {
                return $idValue;
            }
            if (is_string($idValue)) {
                return (int) $idValue;
            }
            return 0;
        }

        $identifier = $user->getUserIdentifier();
        return is_numeric($identifier) ? (int) $identifier : 0;
    }
}
