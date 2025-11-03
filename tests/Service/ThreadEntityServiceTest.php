<?php

namespace ForumBundle\Tests\Service;

use Doctrine\Common\Collections\Collection;
use ForumBundle\Entity\Thread;
use ForumBundle\Service\ThreadEntityService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\UserServiceContracts\UserManagerInterface;

/**
 * @internal
 */
#[CoversClass(ThreadEntityService::class)]
class ThreadEntityServiceTest extends TestCase
{
    private ThreadEntityService $service;

    /** @var EventDispatcherInterface&MockObject */
    private EventDispatcherInterface $eventDispatcher;

    /** @var Security&MockObject */
    private Security $security;

    /** @var UrlGeneratorInterface&MockObject */
    private UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->service = new ThreadEntityService(
            $this->eventDispatcher,
            $this->security,
            $this->urlGenerator
        );
    }

    public function testHandleBeforeCreateThrowsException(): void
    {
        $thread = new Thread();
        $userManager = $this->createMock(UserManagerInterface::class);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('需要实现获取官方用户的逻辑');

        $this->service->handleBeforeCreate($thread, $userManager);
    }

    public function testHandleAfterEditWithEmptyRecord(): void
    {
        $thread = new Thread();
        $form = ['status' => 'audit_pass'];
        $record = [];

        $this->eventDispatcher->expects($this->never())
            ->method('dispatch')
        ;

        $this->service->handleAfterEdit($thread, $form, $record);
    }

    public function testHandleAfterEditWithSameStatus(): void
    {
        $thread = new Thread();
        $form = ['status' => 'audit_pass'];
        $record = ['status' => 'audit_pass'];

        $this->eventDispatcher->expects($this->never())
            ->method('dispatch')
        ;

        $this->service->handleAfterEdit($thread, $form, $record);
    }

    public function testRenderEntityCountReturnsCorrectStructure(): void
    {
        $thread = $this->createMock(Thread::class);
        $thread->method('getCommentCount')->willReturn(5);
        $thread->method('getLikeCount')->willReturn(3);
        $thread->method('getCollectCount')->willReturn(2);

        $result = $this->service->renderEntityCount($thread);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('comments', $result);
        $this->assertArrayHasKey('likes', $result);
        $this->assertArrayHasKey('collects', $result);
        $this->assertEquals(5, $result['comments']);
        $this->assertEquals(3, $result['likes']);
        $this->assertEquals(2, $result['collects']);
    }

    public function testRenderVideoColumnWithEmptyMedia(): void
    {
        $threadMedia = $this->createMock(Collection::class);
        $threadMedia->method('isEmpty')->willReturn(true);

        $thread = $this->createMock(Thread::class);
        $thread->method('getThreadMedia')->willReturn($threadMedia);

        $result = $this->service->renderVideoColumn($thread);

        $this->assertEquals('', $result);
    }

    public function testRenderVideoColumnWithMedia(): void
    {
        $threadMedia = $this->createMock(Collection::class);
        $threadMedia->method('isEmpty')->willReturn(false);

        $thread = $this->createMock(Thread::class);
        $thread->method('getThreadMedia')->willReturn($threadMedia);
        $thread->method('getId')->willReturn('123');

        $result = $this->service->renderVideoColumn($thread);

        $decodedResult = json_decode($result, true);
        $this->assertIsArray($decodedResult);
        $this->assertArrayHasKey('label', $decodedResult);
        $this->assertEquals('查看视频', $decodedResult['label']);
        $this->assertStringContainsString('123', $result);
    }

    public function testRenderUserPhoneColumnReturnsEmptyValue(): void
    {
        $thread = $this->createMock(Thread::class);

        $result = $this->service->renderUserPhoneColumn($thread);

        $this->assertStringContainsString('value', $result);
    }

    public function testGetOtherDataReturnsEmptyString(): void
    {
        $thread = $this->createMock(Thread::class);
        $user = $this->createMock(UserInterface::class);
        $user->method('getUserIdentifier')->willReturn('testuser');

        $thread->method('getUser')->willReturn($user);

        $this->urlGenerator->method('generate')->willReturn('https://example.com');

        $result = $this->service->getOtherData($thread);

        $this->assertEquals('', $result);
    }

    public function testGenerateLockResourceReturnsCorrectFormat(): void
    {
        $thread = $this->createMock(Thread::class);
        $thread->method('getId')->willReturn('456');

        $result = $this->service->generateLockResource($thread);

        $this->assertEquals('lock_forum_thread_456', $result);
    }
}
