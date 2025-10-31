<?php

namespace ForumBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\ForumShareRecord;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;
use Tourze\UserServiceContracts\UserManagerInterface;

class SaveShareRecordService
{
    public function __construct(
        private UserManagerInterface $userManager,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Async]
    public function save(string $userId, string $type, string $threadId): void
    {
        try {
            $user = $this->userManager->loadUserByIdentifier($userId);
        } catch (\Exception $e) {
            return;
        }
        $forumShareRecord = new ForumShareRecord();
        $forumShareRecord->setType($type);
        $forumShareRecord->setUser($user);
        $forumShareRecord->setSourceId($threadId);
        $this->entityManager->persist($forumShareRecord);
        $this->entityManager->flush();
    }
}
