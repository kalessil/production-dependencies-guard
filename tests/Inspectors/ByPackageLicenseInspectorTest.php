<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface as PackageContract;
use PHPUnit\Framework\TestCase;

final class ByPackageLicenseInspectorTest extends TestCase
{
    public function testComponentWithAcceptedLicenses(): void
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->atLeastOnce())->method('getLicense')->willReturn(
            [''],
            [],
            ['MIT '],
            ['MIT'],
            ['Apache'],
            ['mit', 'apache'],
            ['mit', 'proprietary', 'apache'],
            ['gpl', 'proprietary']
        );

        $component = new ByPackageLicenseInspector([
            'mit',
            'apache',
        ]);

        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
    }

    public function testComponentWithoutAcceptedLicenses(): void
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->atLeastOnce())->method('getLicense')->willReturn(
            [''],
            [],
            ['MIT'],
        );

        $component = new ByPackageLicenseInspector([]);

        $this->assertTrue($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
    }
}