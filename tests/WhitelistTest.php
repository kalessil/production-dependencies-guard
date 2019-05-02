<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Package\CompletePackageInterface;
use PHPUnit\Framework\TestCase;

final class WhitelistTest extends TestCase
{
    /** @covers \Kalessil\Composer\Plugins\ProductionDependenciesGuard\Whitelist::<public> */
    public function testComponent()
    {
        $package = $this->createMock(CompletePackageInterface::class);
        $package->expects($this->atLeastOnce())->method('getName')->willReturn('package1', 'package2', 'package3');

        $component = new Whitelist(['', '...', 'white-list:package1', 'white-list:package2']);

        $this->assertTrue($component->canUse($package));
        $this->assertTrue($component->canUse($package));
        $this->assertFalse($component->canUse($package));
    }
}