<?php

declare(strict_types=1);

namespace ForumBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use ForumBundle\Entity\ForumShareRecord;
use ForumBundle\Service\SaveShareRecordService;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\UserServiceContracts\UserManagerInterface;

/**
 * 保存分享记录服务测试
 *
 * @internal
 */
#[CoversClass(SaveShareRecordService::class)]
#[RunTestsInSeparateProcesses] final class SaveShareRecordServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 测试不需要特殊的初始化逻辑
    }

    private function getSaveShareRecordService(
        ?UserManagerInterface $userManager = null,
        ?EntityManagerInterface $entityManager = null,
    ): SaveShareRecordService {
        // 在需要Mock依赖时，应在容器初始化前设置Mock服务
        if (null !== $userManager) {
            self::getContainer()->set(UserManagerInterface::class, $userManager);
        }
        if (null !== $entityManager) {
            self::getContainer()->set(EntityManagerInterface::class, $entityManager);
        }

        // 从容器中获取服务实例（符合集成测试的最佳实践）
        return self::getService(SaveShareRecordService::class);
    }

    public function testInstantiation(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getSaveShareRecordService(
            $userManager,
            $entityManager
        );
        $this->assertInstanceOf(SaveShareRecordService::class, $service);
    }

    public function testSaveSuccessfully(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $user = $this->createMock(UserInterface::class);

        $userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('user123')
            ->willReturn($user)
        ;

        $entityManager->expects($this->once())
            ->method('persist')
            ->with(Assert::isInstanceOf(ForumShareRecord::class))
        ;

        $entityManager->expects($this->once())
            ->method('flush')
        ;

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getSaveShareRecordService(
            $userManager,
            $entityManager
        );
        $service->save('user123', 'thread', 'thread456');
    }

    public function testSaveWithUserNotFoundException(): void
    {
        $userManager = $this->createMock(UserManagerInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $userManager->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with('user123')
            ->willThrowException(new \Exception('User not found'))
        ;

        $entityManager->expects($this->never())
            ->method('persist')
        ;

        $entityManager->expects($this->never())
            ->method('flush')
        ;

        // 直接创建服务实例，避免容器中已初始化服务的冲突
        $service = $this->getSaveShareRecordService(
            $userManager,
            $entityManager
        );
        $service->save('user123', 'thread', 'thread456');
    }
}
