<?php

namespace ForumBundle\Tests\EntityListener;

use ForumBundle\Entity\ThreadMedia;
use ForumBundle\EntityListener\ThreadMediaListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(ThreadMediaListener::class)]
#[RunTestsInSeparateProcesses]
final class ThreadMediaListenerTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 空实现，没有特殊的初始化需求
    }

    public function testConstructorWorksWithoutParameters(): void
    {
        $listener = self::getService(ThreadMediaListener::class);
        $this->assertInstanceOf(ThreadMediaListener::class, $listener);
    }

    #[DataProvider('prePersistDataProvider')]
    public function testPrePersist(?string $initialType, ?string $initialPath, ?string $initialThumbnail, string $expectedType, string $expectedPath, ?string $expectedThumbnail): void
    {
        /*
         * 使用具体类 ThreadMedia 的原因：
         * 1. ThreadMediaListener 的 prePersist 方法直接依赖于 ThreadMedia 实体类，没有接口抽象
         * 2. 这是 Doctrine 实体监听器的标准模式，监听器通常与具体实体类紧密耦合
         * 3. ThreadMedia 作为实体类，其行为和数据结构是稳定的，适合作为测试边界
         */
        $threadMedia = $this->createMock(ThreadMedia::class);

        $this->setupTypeExpectations($threadMedia, $initialType);

        [$pathCallCount, $thumbnailCallCount] = $this->calculateCallCounts($initialPath, $initialThumbnail);

        $this->setupGetterExpectations($threadMedia, $initialPath, $initialThumbnail, $pathCallCount, $thumbnailCallCount);

        $this->setupSetterExpectations($threadMedia, $initialPath, $initialThumbnail);

        $listener = self::getService(ThreadMediaListener::class);
        $listener->prePersist($threadMedia);

        $this->assertInstanceOf(ThreadMediaListener::class, $listener);
    }

    /**
     * @return array<string, array{
     * initialType: ?string, initialPath: ?string, initialThumbnail: ?string, expectedType: string, expectedPath: string, expectedThumbnail: ?string}>
     */
    public static function prePersistDataProvider(): array
    {
        return [
            'type_is_null' => [
                'initialType' => null,
                'initialPath' => '/path/to/image.jpg',
                'initialThumbnail' => '/path/to/thumb.jpg',
                'expectedType' => 'image',
                'expectedPath' => '/path/to/image.jpg',
                'expectedThumbnail' => '/path/to/thumb.jpg',
            ],
            'thumbnail_is_null_with_path' => [
                'initialType' => 'video',
                'initialPath' => '/path/to/video.mp4',
                'initialThumbnail' => null,
                'expectedType' => 'video',
                'expectedPath' => '/path/to/video.mp4',
                'expectedThumbnail' => '/path/to/video.mp4',
            ],
            'path_is_null_with_thumbnail' => [
                'initialType' => 'image',
                'initialPath' => null,
                'initialThumbnail' => '/path/to/thumb.jpg',
                'expectedType' => 'image',
                'expectedPath' => '/path/to/thumb.jpg',
                'expectedThumbnail' => '/path/to/thumb.jpg',
            ],
            'all_values_set' => [
                'initialType' => 'document',
                'initialPath' => '/path/to/doc.pdf',
                'initialThumbnail' => '/path/to/doc-thumb.jpg',
                'expectedType' => 'document',
                'expectedPath' => '/path/to/doc.pdf',
                'expectedThumbnail' => '/path/to/doc-thumb.jpg',
            ],
            'only_type_is_null' => [
                'initialType' => null,
                'initialPath' => null,
                'initialThumbnail' => null,
                'expectedType' => 'image',
                'expectedPath' => '',
                'expectedThumbnail' => null,
            ],
        ];
    }

    /**
     * @param MockObject&ThreadMedia $threadMedia
     */
    private function setupTypeExpectations($threadMedia, ?string $initialType): void
    {
        $threadMedia->expects($this->once())
            ->method('getType')
            ->willReturn($initialType)
        ;

        if (null === $initialType) {
            $threadMedia->expects($this->once())
                ->method('setType')
                ->with('image')
            ;
        } else {
            $threadMedia->expects($this->never())
                ->method('setType')
            ;
        }
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function calculateCallCounts(?string $initialPath, ?string $initialThumbnail): array
    {
        $pathCallCount = 2;
        $thumbnailCallCount = 1;

        if (null !== $initialPath && null === $initialThumbnail) {
            $pathCallCount = 3;
            $thumbnailCallCount = 1;
        } elseif (null === $initialPath && null !== $initialThumbnail) {
            $pathCallCount = 2;
            $thumbnailCallCount = 2;
        } elseif (null === $initialPath && null === $initialThumbnail) {
            $pathCallCount = 2;
            $thumbnailCallCount = 1;
        }

        return [$pathCallCount, $thumbnailCallCount];
    }

    /**
     * @param MockObject&ThreadMedia $threadMedia
     */
    private function setupGetterExpectations($threadMedia, ?string $initialPath, ?string $initialThumbnail, int $pathCallCount, int $thumbnailCallCount): void
    {
        $threadMedia->expects($this->exactly($pathCallCount))
            ->method('getPath')
            ->willReturn($initialPath)
        ;

        $threadMedia->expects($this->exactly($thumbnailCallCount))
            ->method('getThumbnail')
            ->willReturn($initialThumbnail)
        ;
    }

    /**
     * @param MockObject&ThreadMedia $threadMedia
     */
    private function setupSetterExpectations($threadMedia, ?string $initialPath, ?string $initialThumbnail): void
    {
        if (null !== $initialPath && null === $initialThumbnail) {
            $threadMedia->expects($this->once())
                ->method('setThumbnail')
                ->with($initialPath)
            ;
        } elseif (null === $initialPath && null !== $initialThumbnail) {
            $threadMedia->expects($this->once())
                ->method('setPath')
                ->with($initialThumbnail)
            ;
        } else {
            $threadMedia->expects($this->never())
                ->method('setThumbnail')
            ;
            $threadMedia->expects($this->never())
                ->method('setPath')
            ;
        }
    }
}
