<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers;

use PHPUnit\Framework\TestCase;

final class FromComposerLockSupplierTest extends TestCase
{
    public function testComponent(): void
    {
        putenv(sprintf('COMPOSER=%s/../data/composer.json', __DIR__));
        $component= new FromComposerLockSupplier();
        $this->assertSame(['kalessil/production-dependencies-guard-lock'], $component->packages());
        $this->assertSame(['kalessil/production-dependencies-guard-lock'], $component->why('phpunit/phpunit'));
        $this->assertSame(['manifest'], $component->why('vendor/package'));
        putenv('COMPOSER=');
    }
}