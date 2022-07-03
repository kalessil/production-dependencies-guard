<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Package\CompletePackageInterface;
use PHPUnit\Framework\TestCase;

final class WhitelistTest extends TestCase
{
    public function testComponent(): void
    {
        $package = $this->createMock(CompletePackageInterface::class);
        $package->expects($this->atLeastOnce())->method('getName')->willReturn(
            'package1',
            'Package2',
            'package3',
            '...',
            'vendor/package',
        );

        $component = new Whitelist([
            'package1',
            'package2',
        ]);

        $this->assertTrue($component->canUse($package));
        $this->assertTrue($component->canUse($package));
        $this->assertFalse($component->canUse($package));
        $this->assertFalse($component->canUse($package));
        $this->assertFalse($component->canUse($package));
    }
}