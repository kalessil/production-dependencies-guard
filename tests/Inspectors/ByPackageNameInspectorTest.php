<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface as PackageContract;
use PHPUnit\Framework\TestCase;

final class ByPackageNameInspectorTest extends TestCase
{
    /**
     * @covers \Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageNameInspector::<public>
     * @covers \Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageNameInspector::<private>
     */
    public function testComponent()
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->atLeastOnce())->method('getName')->willReturn(...[
            '',
            'phpunit/phpunit',
            'roave/security-advisories'
        ]);

        $component = new ByPackageNameInspector();
        $this->assertTrue($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
        $this->assertFalse($component->canUse($mock));
    }
}