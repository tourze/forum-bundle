<?php

namespace ForumBundle\Service;

use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCommentLike;
use ForumBundle\Repository\ThreadCommentRepository;
use Symfony\Bundle\SecurityBundle\Security;

readonly class ThreadCommentService
{
    public function __construct(
        private ThreadCommentRepository $threadCommentRepository,
        private Security $security,
    ) {
    }

    /**
     * @return array<mixed>
     */
    public function getCommentListByThreadId(Thread $thread): array
    {
        $user = $this->security->getUser();
        if (null === $user) {
            throw new \RuntimeException('请先登录');
        }

        $qb = $this->threadCommentRepository->createQueryBuilder('c')
            ->select([
                'c.id',
                'c.parentId',
                'c.content',
                'c.createTime',
                'u.id as userId',
                'u.nickName',
                'u.avatar',
                '(SELECT count(1) AS total1 FROM ' . ThreadCommentLike::class . ' l WHERE c.id = l.threadComment and l.user =:user2 ) as isLike',
                '(SELECT count(1) AS total2 FROM ' . ThreadCommentLike::class . ' l2 WHERE c.id = l2.threadComment ) as likeCount',
            ])
            ->where('c.thread =:thread')
            ->setParameter('thread', $thread)
            ->setParameter('user2', $user)
            ->leftJoin('c.user', 'u')
            ->orderBy('c.id', 'DESC')
        ;
        /** @var array<int, array<string, mixed>> $list */
        $list = $qb->getQuery()->getArrayResult();

        return $this->arrayToTree($list);
    }

    /**
     * @param array<int, array<string, mixed>> $list
     *
     * @return array<int, array<string, mixed>>
     */
    private function arrayToTree(array $list): array
    {
        // Create indexed copy of all items
        $indexed = $this->createIndexedItems($list);

        // Build tree structure
        return $this->buildTreeStructure($list, $indexed);
    }

    /**
     * @param array<int, array<string, mixed>> $list
     *
     * @return array<int|string, array<string, mixed>>
     */
    private function createIndexedItems(array $list): array
    {
        $indexed = [];
        foreach ($list as $item) {
            $itemId = $item['id'] ?? null;
            if (null === $itemId || (!is_int($itemId) && !is_string($itemId))) {
                continue;
            }
            $indexed[$itemId] = $item;
        }

        return $indexed;
    }

    /**
     * @param array<int, array<string, mixed>>        $list
     * @param array<int|string, array<string, mixed>> $indexed
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildTreeStructure(array $list, array $indexed): array
    {
        $tree = [];
        foreach ($list as $item) {
            if ($this->isRootItem($item)) {
                $result = $this->addRootItem($tree, $item, $indexed);
                $tree = $result['tree'];
                continue;
            }

            $indexed = $this->addChildItem($item, $indexed);
        }

        return $tree;
    }

    /**
     * @param array<int, array<string, mixed>>        $tree
     * @param array<string, mixed>                    $item
     * @param array<int|string, array<string, mixed>> $indexed
     *
     * @return array{tree: array<int, array<string, mixed>>}
     */
    private function addRootItem(array $tree, array $item, array $indexed): array
    {
        $itemId = $item['id'] ?? null;
        if (null !== $itemId && (is_int($itemId) || is_string($itemId)) && isset($indexed[$itemId])) {
            $indexedItem = $indexed[$itemId];
            if (is_array($indexedItem)) {
                $tree[] = $indexedItem;
            }
        }

        return ['tree' => $tree];
    }

    /**
     * @param array<string, mixed>                    $item
     * @param array<int|string, array<string, mixed>> $indexed
     *
     * @return array<int|string, array<string, mixed>>
     */
    private function addChildItem(array $item, array $indexed): array
    {
        $parentId = $item['parentId'] ?? null;
        if (!$this->isValidKey($parentId)) {
            return $indexed;
        }
        // PHPStan 现在知道 $parentId 是 int|string
        assert(is_int($parentId) || is_string($parentId));

        if (!isset($indexed[$parentId])) {
            return $indexed;
        }

        $parentItem = $indexed[$parentId];
        if (!is_array($parentItem)) {
            return $indexed;
        }

        $itemId = $item['id'] ?? null;
        if (!$this->isValidKey($itemId)) {
            return $indexed;
        }
        // PHPStan 现在知道 $itemId 是 int|string
        assert(is_int($itemId) || is_string($itemId));

        if (!isset($indexed[$itemId])) {
            return $indexed;
        }

        $childItem = $indexed[$itemId];
        if (!is_array($childItem)) {
            return $indexed;
        }

        if (!isset($parentItem['children']) || !is_array($parentItem['children'])) {
            $parentItem['children'] = [];
        }

        $parentItem['children'][] = $childItem;
        $indexed[$parentId] = $parentItem;

        return $indexed;
    }

    /**
     * @param mixed $key
     */
    private function isValidKey($key): bool
    {
        return is_int($key) || is_string($key);
    }

    /**
     * @param array<string, mixed> $item
     */
    private function isRootItem(array $item): bool
    {
        return 0 === $item['parentId'];
    }
}
