# Forum Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/forum-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/forum-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/forum-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/forum-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/forum-bundle.svg?style=flat-square)](https://codecov.io/gh/tourze/forum-bundle)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/forum-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/forum-bundle)
[![License](https://img.shields.io/packagist/l/tourze/forum-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/forum-bundle)

为 Symfony 应用程序提供论坛、帖子和社区功能的综合论坛系统包。

## 当前状态

✅ **开发状态**: 该包已稳定，可用于生产环境。

- ✅ 核心功能稳定且可用
- ✅ 完成了主要代码重构，提高了可维护性
- ✅ **PHPStan 分析通过** 零错误（从72个错误减少到0个）
- ✅ **ThreadService 复杂度已降低** - 提取 ThreadDetailBuilder 服务实现关注点分离
- ✅ **测试隔离性改进** - 所有测试类现在使用 `#[RunTestsInSeparateProcesses]` 注解
- ⚠️ 部分集成测试可能因缺少服务依赖（UserManagerInterface）而失败

## 目录

- [功能特性](#功能特性)
- [依赖关系](#依赖关系)
- [安装](#安装)
- [配置](#配置)
- [控制台命令](#控制台命令)
- [快速开始](#快速开始)
- [实体](#实体)
- [管理界面](#管理界面)
- [高级用法](#高级用法)
- [环境变量](#环境变量)
- [许可证](#许可证)

## 功能特性

- **帖子管理** - 创建、编辑、发布和审核帖子
- **用户互动** - 点赞、评论、收藏和分享帖子
- **自动审核** - 基于时间的自动上架和下架帖子
- **统计与排名** - 追踪访问统计并维护排名系统
- **维度评分** - 计算帖子的多维度评分
- **管理界面** - 完整的 EasyAdmin CRUD 控制器
- **事件系统** - 丰富的事件系统支持自定义
- **多媒体支持** - 支持帖子中的图片和其他媒体内容

## 依赖关系

该包需要以下依赖：

### 核心依赖
- `doctrine/orm` (^3.0) - 数据库 ORM
- `doctrine/doctrine-bundle` (^2.13) - Symfony Doctrine 集成
- `symfony/framework-bundle` (^6.4) - Symfony 框架
- `easycorp/easyadmin-bundle` (^4) - 管理界面

### 内部依赖
- `tourze/user-service-contracts` - 用户管理接口
- `tourze/doctrine-*` bundles - 各种 Doctrine 扩展
- `tourze/json-rpc-*` bundles - JSON-RPC API 支持

## 安装

```bash
composer require tourze/forum-bundle
```

## 配置

安装后，在您的 Symfony 应用程序中配置包：

### 1. 注册包

如果您使用 Symfony Flex，该包应自动注册。否则，将其添加到您的 `config/bundles.php`：

```php
<?php

return [
    // ...
    ForumBundle\ForumBundle::class => ['all' => true],
];
```

### 2. 数据库迁移

创建并运行数据库迁移：

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### 3. 加载示例数据（可选）

为开发环境加载示例论坛数据：

```bash
php bin/console doctrine:fixtures:load --group=forum
```

## 控制台命令

该包提供了多个控制台命令用于管理论坛操作：

### 帖子自动化
- `forum:auto-release-thread` - 自动发布定时帖子（每分钟运行）
- `forum:auto-take-down-thread` - 自动下架过期帖子（每分钟运行）

### 统计管理
- `forum:update-thread-visit-stat` - 更新没有统计数据的帖子的访问统计
- `forum:calc-dimension-value` - 计算所有帖子的维度得分（每30分钟运行）

### 排名系统
- `forum:update-thread-stat-collect-rank` - 更新帖子收藏排名（每小时运行）
- `forum:update-thread-stat-comment-rank` - 更新帖子评论排名（每小时运行）
- `forum:update-thread-stat-like-rank` - 更新帖子点赞排名（每小时运行）
- `forum:update-thread-stat-share-rank` - 更新帖子分享排名（每小时运行）
- `forum:update-thread-stat-visit-rank` - 更新帖子访问排名（每小时运行）

## 环境变量

排名命令可通过环境变量进行控制：

```bash
# 启用/禁用排名任务 (0=禁用, 1=启用)
ENABLE_THREAD_STAT_RANK_TASK=1

# 参与排名的帖子数量 (默认: 50)
THREAD_RANK_LIMIT=50
```

## 快速开始

### 基本使用

```php
<?php

use ForumBundle\Entity\Thread;
use ForumBundle\Service\ThreadService;

// 创建新帖子
$thread = new Thread();
$thread->setTitle('我的帖子标题');
$thread->setContent('帖子内容...');

// 使用帖子服务
$threadService = $container->get(ThreadService::class);
$threadService->updateAllStat($thread);
```

### 事件监听器

该包提供各种事件以支持自定义：

```php
<?php

use ForumBundle\Event\AfterPublishThread;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomForumSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AfterPublishThread::class => 'onThreadPublished',
        ];
    }

    public function onThreadPublished(AfterPublishThread $event): void
    {
        // 处理帖子发布
        $thread = $event->getThread();
        // 您的自定义逻辑
    }
}
```

## 实体

该包提供以下主要实体：

- `Thread` - 论坛帖子/文章
- `ThreadComment` - 帖子评论
- `ThreadLike` - 帖子点赞
- `ThreadCollect` - 帖子收藏/书签
- `Channel` - 论坛频道/分类
- `Topic` - 帖子话题/标签
- `VisitStat` - 帖子访问统计
- `Dimension` - 多维度评分系统

## 管理界面

该包包含 EasyAdmin 控制器，用于通过 Web 界面管理所有实体：

- 论坛帖子、评论和互动
- 用户管理和审核
- 统计和分析
- 频道和话题管理

## 高级用法

### 自定义帖子处理

扩展帖子服务以实现自定义业务逻辑：

```php
<?php

use ForumBundle\Service\ThreadService;
use ForumBundle\Entity\Thread;

class CustomThreadService extends ThreadService
{
    public function processThread(Thread $thread): void
    {
        // 自定义验证
        if (strlen($thread->getContent()) < 50) {
            throw new \InvalidArgumentException('帖子内容太短');
        }
        
        // 更新统计
        $this->updateAllStat($thread);
        
        // 自定义后处理
        $this->notifyModerators($thread);
    }
    
    private function notifyModerators(Thread $thread): void
    {
        // 您的自定义通知逻辑
    }
}
```

### 高级事件处理

创建复杂的事件工作流：

```php
<?php

use ForumBundle\Event\AfterPublishThread;
use ForumBundle\Event\BeforePublishThread;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdvancedForumSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BeforePublishThread::class => ['validateThread', 100],
            AfterPublishThread::class => [
                ['updateStatistics', 50],
                ['sendNotifications', 10],
                ['updateCache', -10],
            ],
        ];
    }
    
    public function validateThread(BeforePublishThread $event): void
    {
        $thread = $event->getThread();
        
        // 验证帖子内容
        if ($this->containsSensitiveContent($thread)) {
            $event->stopPropagation();
            throw new \Exception('帖子包含敏感内容');
        }
    }
    
    public function updateStatistics(AfterPublishThread $event): void
    {
        // 更新论坛统计
    }
    
    public function sendNotifications(AfterPublishThread $event): void
    {
        // 发送通知给订阅者
    }
    
    public function updateCache(AfterPublishThread $event): void
    {
        // 更新缓存的论坛数据
    }
}
```

### 自定义排名算法

实现自定义排名策略：

```php
<?php

use ForumBundle\Entity\VisitStat;

class CustomRankingService
{
    public function calculateCustomRank(VisitStat $stat): int
    {
        $score = 0;
        
        // 为不同因素分配权重
        $score += $stat->getLikeTotal() * 3;      // 点赞权重3
        $score += $stat->getCommentTotal() * 2;   // 评论权重2
        $score += $stat->getCollectCount() * 5;   // 收藏权重5
        $score += $stat->getVisitTotal() * 1;     // 访问权重1
        
        // 应用时间衰减
        $daysSinceCreation = $this->getDaysSinceCreation($stat);
        $score = $score / (1 + $daysSinceCreation * 0.1);
        
        return (int) $score;
    }
    
    private function getDaysSinceCreation(VisitStat $stat): int
    {
        $now = new \DateTime();
        $created = $stat->getThread()->getCreateTime();
        return $now->diff($created)->days;
    }
}
```

## 许可证

MIT 许可证。更多信息请参阅 [许可证文件](LICENSE)。