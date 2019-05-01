<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface as PackageContract;
use PHPUnit\Framework\TestCase;

final class StubInspectorTest extends TestCase
{
    /** @covers \Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\StubInspector::<public> */
    public function testComponent()
    {
        $mock = $this->createMock(PackageContract::class);
        $mock->expects($this->never())->method($this->anything());

        $this->assertTrue((new StubInspector())->canUse($mock));
    }
}