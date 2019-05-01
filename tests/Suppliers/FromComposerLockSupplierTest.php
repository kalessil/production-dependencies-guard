<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers;

use PHPUnit\Framework\TestCase;

final class FromComposerLockSupplierTest extends TestCase
{
    /** @covers \Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\FromComposerLockSupplier::<public> */
    public function testComponent() {
        putenv(sprintf('COMPOSER=%s/../data/composer.json', __DIR__));
        $this->assertSame(['kalessil/production-dependencies-guard-lock'], (new FromComposerLockSupplier())->packages());
        putenv('COMPOSER=');
    }
}