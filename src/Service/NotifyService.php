<?php

namespace ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Repository\MessageNotificationRepository;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
readonly class NotifyService
{
    public function __construct(
        private MessageNotificationRepository $messageNotificationRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 发送消息
     */
    public function send(MessageNotification $messageNotification): void
    {
        // 有ID的情况下，update，目前只能直接save
        if (null !== $messageNotification->getId()) {
            $this->entityManager->persist($messageNotification);
            $this->entityManager->flush();

            return;
        }
        // 直接插入
        $this->entityManager->persist($messageNotification);
        $this->entityManager->flush();
    }

    /**
     * 根据类型获取通知
     *
     * @return array<mixed>
     */
    public function getListByType(int $type): array
    {
        $qb = $this->messageNotificationRepository->createQueryBuilder('m')
            ->select([
                'm.id',
                'm.content',
                'm.createTime',
                'u.id as userId',
                'u.nickName',
                'u.avatar',
                's.id as senderId',
                's.nickName as senderNickname',
                's.avatar as senderAvatar',
            ])
            ->where('m.type =:type')
            ->andWhere('m.deleted =:deleted')
            ->setParameter('type', $type)
            ->setParameter('deleted', 0)
            ->leftJoin('m.user', 'u')
            ->leftJoin('m.sender', 's')
            ->orderBy('m.id', 'DESC')
        ;

        $messageList = $qb->getQuery()->getArrayResult();

        $list = [];
        foreach ($messageList as $item) {
            // 确保 $item 是数组类型
            if (!is_array($item)) {
                continue;
            }
            $list[] = [
                'id' => $item['id'] ?? null,
                'content' => $item['content'] ?? '',
                'createTime' => $item['createTime'] ?? null,
                'userId' => $item['userId'] ?? null,
            ];
        }

        return $list;
    }
}
