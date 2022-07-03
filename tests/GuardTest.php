<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Repository\InstalledRepositoryInterface;
use Composer\Repository\RepositoryManager;
use PHPUnit\Framework\TestCase;

final class GuardTest extends TestCase
{
    public function testSubscribedEvents(): void
    {
        $this->assertSame([
            'post-install-cmd' => ['checkGeneric'],
            'post-update-cmd' => ['checkGeneric'],
        ], Guard::getSubscribedEvents());
    }

    public function testGenericBehaviourReporting(): void
    {
        $composer = $this->getMockBuilder(Composer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRepositoryManager'])
            ->getMock();
        $repositoryManager = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLocalRepository'])
            ->getMock();
        $installedRepository = $this->getMockBuilder(InstalledRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPackages'])
            ->getMockForAbstractClass();
        $composer->expects($this->atLeastOnce())->method('getRepositoryManager')->willReturn($repositoryManager);
        $repositoryManager->expects($this->atLeastOnce())->method('getLocalRepository')->willReturn($installedRepository);
        $installedRepository->expects($this->atLeastOnce())->method('getPackages')->willReturnCallback(function (): array {
            $pass = $this->createMock(CompletePackageInterface::class);
            $pass->expects($this->atLeastOnce())->method('getName')->willReturn('kalessil/kalessil');
            $pass->expects($this->atLeastOnce())->method('getType')->willReturn('library');

            $decline = $this->createMock(CompletePackageInterface::class);
            $decline->expects($this->atLeastOnce())->method('getName')->willReturn('PHPUnit/phpunit');
            $decline->expects($this->atLeastOnce())->method('getType')->willReturn('phpcodesniffer-standard');

            $abandoned = $this->createMock(CompletePackageInterface::class);
            $abandoned->expects($this->atLeastOnce())->method('getName')->willReturn('vendor/abandoned');
            $abandoned->expects($this->atLeastOnce())->method('getType')->willReturn('library');
            $abandoned->expects($this->atLeastOnce())->method('isAbandoned')->willReturn(true);

            $debug = $this->createMock(CompletePackageInterface::class);
            $debug->expects($this->atLeastOnce())->method('getName')->willReturn('vendor/debug');
            $debug->expects($this->atLeastOnce())->method('getType')->willReturn('library');
            $debug->expects($this->atLeastOnce())->method('getDescription')->willReturn('debug');

            $guards = $this->createMock(CompletePackageInterface::class);
            $guards->expects($this->atLeastOnce())->method('getName')->willReturn('vendor/guards');
            $guards->method('isAbandoned')->willReturn(true);
            $guards->expects($this->atLeastOnce())->method('getLicense')->willReturn(['MIT']);

            return [$pass, $decline, $abandoned, $debug, $guards];
        });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(<<<'___EOM___'
Dependencies guard has found violations in require-dependencies (source: manifest):
 - kalessil/kalessil (via manifest): license
 - phpunit/phpunit (via manifest): dev-package-name, dev-package-type, license
 - vendor/abandoned (via manifest): license, abandoned
 - vendor/debug (via manifest): license, description
___EOM___
        );

        putenv(sprintf('COMPOSER=%s/data/activate-additional-features.json', __DIR__));
        $component = new Guard();
        $component->activate($composer, $this->createMock(IOInterface::class));
        $component->checkGeneric();
    }

    public function testGenericBehaviourPassing(): void
    {
        $composer = $this->getMockBuilder(Composer::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRepositoryManager'])
            ->getMock();
        $repositoryManager = $this->getMockBuilder(RepositoryManager::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLocalRepository'])
            ->getMock();
        $installedRepository = $this->getMockBuilder(InstalledRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPackages'])
            ->getMockForAbstractClass();
        $composer->expects($this->atLeastOnce())->method('getRepositoryManager')->willReturn($repositoryManager);
        $repositoryManager->expects($this->atLeastOnce())->method('getLocalRepository')->willReturn($installedRepository);
        $installedRepository->expects($this->atLeastOnce())->method('getPackages')->willReturnCallback(function (): array {
            $pass = $this->createMock(CompletePackageInterface::class);
            $pass->expects($this->atLeastOnce())->method('getName')->willReturn('kalessil/kalessil');
            $pass->expects($this->atLeastOnce())->method('getType')->willReturn('library');

            return [$pass];
        });

        putenv(sprintf('COMPOSER=%s/data/activate-none-features.json', __DIR__));
        $component = new Guard();
        $component->activate($composer, $this->createMock(IOInterface::class));
        $component->checkGeneric();
    }

    public function testDeactivate(): void
    {
        $composer = $this->getMockBuilder(Composer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $composer->expects($this->never())->method($this->anything());

        $component = new Guard();
        $component->deactivate($composer, $this->createMock(IOInterface::class));
    }

    public function testUninstall(): void
    {
        $composer = $this->getMockBuilder(Composer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $composer->expects($this->never())->method($this->anything());

        $component = new Guard();
        $component->uninstall($composer, $this->createMock(IOInterface::class));
    }
}