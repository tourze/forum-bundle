<?php

namespace ForumBundle\Service;

use ForumBundle\Entity\Channel;
use ForumBundle\Entity\ChannelSubscribe;
use ForumBundle\Entity\Dimension;
use ForumBundle\Entity\ForumShareRecord;
use ForumBundle\Entity\MessageNotification;
use ForumBundle\Entity\SortingRule;
use ForumBundle\Entity\Thread;
use ForumBundle\Entity\ThreadCollect;
use ForumBundle\Entity\ThreadComment;
use ForumBundle\Entity\ThreadCommentLike;
use ForumBundle\Entity\ThreadDimension;
use ForumBundle\Entity\ThreadLike;
use ForumBundle\Entity\ThreadMedia;
use ForumBundle\Entity\ThreadRelation;
use ForumBundle\Entity\VisitStat;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\EasyAdminMenuBundle\Attribute\MenuProvider;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

#[MenuProvider]
#[Autoconfigure(public: true)]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private ?LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $this->linkGenerator) {
            return;
        }

        if (null === $item->getChild('帖子管理')) {
            $item->addChild('帖子管理');
        }
        $postManagement = $item->getChild('帖子管理');
        if (null !== $postManagement) {
            $postManagement->addChild('用户发帖')->setUri($this->linkGenerator->getCurdListPage(Thread::class));
            $postManagement->addChild('帖子分类')->setUri($this->linkGenerator->getCurdListPage(Channel::class));
            $postManagement->addChild('帖子评论')->setUri($this->linkGenerator->getCurdListPage(ThreadComment::class));
            $postManagement->addChild('评论点赞')->setUri($this->linkGenerator->getCurdListPage(ThreadCommentLike::class));
            $postManagement->addChild('帖子点赞')->setUri($this->linkGenerator->getCurdListPage(ThreadLike::class));
            $postManagement->addChild('帖子收藏')->setUri($this->linkGenerator->getCurdListPage(ThreadCollect::class));
            $postManagement->addChild('帖子媒体')->setUri($this->linkGenerator->getCurdListPage(ThreadMedia::class));
            $postManagement->addChild('帖子关系')->setUri($this->linkGenerator->getCurdListPage(ThreadRelation::class));
            $postManagement->addChild('帖子维度')->setUri($this->linkGenerator->getCurdListPage(ThreadDimension::class));
        }

        if (null === $item->getChild('用户管理')) {
            $item->addChild('用户管理');
        }
        $userManagement = $item->getChild('用户管理');
        if (null !== $userManagement) {
            $userManagement->addChild('频道订阅')->setUri($this->linkGenerator->getCurdListPage(ChannelSubscribe::class));
            $userManagement->addChild('消息通知')->setUri($this->linkGenerator->getCurdListPage(MessageNotification::class));
        }

        if (null === $item->getChild('系统管理')) {
            $item->addChild('系统管理');
        }
        $systemManagement = $item->getChild('系统管理');
        if (null !== $systemManagement) {
            $systemManagement->addChild('排序规则')->setUri($this->linkGenerator->getCurdListPage(SortingRule::class));
            $systemManagement->addChild('维度管理')->setUri($this->linkGenerator->getCurdListPage(Dimension::class));
            $systemManagement->addChild('分享记录')->setUri($this->linkGenerator->getCurdListPage(ForumShareRecord::class));
            $systemManagement->addChild('访问统计')->setUri($this->linkGenerator->getCurdListPage(VisitStat::class));
        }
    }
}
