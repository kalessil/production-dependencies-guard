<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface as PackageContract;
use PHPUnit\Framework\TestCase;

final class ByPackageTypeInspectorTest extends TestCase
{
    public function testComponent(): void
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->atLeastOnce())->method('getType')->willReturn(...[
            '',
            'library',
            'phpcodesniffer-standard',
            'PhpCodeSniffer-standard'
        ]);

        $component = new ByPackageTypeInspector();
        $this->assertTrue($component->canUse($mock));
        $this->assertTrue($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
    }
}