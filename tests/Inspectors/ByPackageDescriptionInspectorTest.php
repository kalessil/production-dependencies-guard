<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface as PackageContract;
use PHPUnit\Framework\TestCase;

final class ByPackageDescriptionInspectorTest extends TestCase
{
    public function testWithKeywords(): void
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->atLeastOnce())->method('getKeywords')->willReturn(
            [],
            ['keyword'],
            ['debug'],
            ['DEBUG']
        );

        $component = new ByPackageDescriptionInspector();
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
    }

    public function testWithDescription(): void
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->atLeastOnce())->method('getDescription')->willReturn(
            '',
            '...',
            ' debug ',
            ' DEBUG ',
            'static analysis',
            'static code analysis',
            'static analyzer',
            'static code analyzer',
            'STATIC CODE ANALYZER',
        );

        $component = new ByPackageDescriptionInspector();

        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
    }

    public function testIgnoredPackages(): void
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->atLeastOnce())->method('getDescription')->willReturn('debug');
        $mock->expects($this->atLeastOnce())->method('getName')->willReturn(
            'vendor/package',
            'symfony/DEBUG',
            'symfony/debug',
            'symfony/var-dumper',
            'symfony/error-handler',
        );

        $component = new ByPackageDescriptionInspector();

        $this->assertFalse($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
    }
}