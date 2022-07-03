<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface as PackageContract;
use PHPUnit\Framework\TestCase;

final class ByPackageNameInspectorTest extends TestCase
{
    public function testComponent(): void
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->atLeastOnce())->method('getName')->willReturn(...[
            '',
            'phpunit/phpunit',
            'roave/security-advisories',
            'Roave/Security-Advisories',
        ]);

        $component = new ByPackageNameInspector();
        $this->assertTrue($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
    }
}