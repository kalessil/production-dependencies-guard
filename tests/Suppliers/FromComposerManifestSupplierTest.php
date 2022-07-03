<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers;

use PHPUnit\Framework\TestCase;

final class FromComposerManifestSupplierTest extends TestCase
{
    public function testComponent(): void
    {
        putenv(sprintf('COMPOSER=%s/../data/composer.json', __DIR__));
        $component = new FromComposerManifestSupplier();
        $this->assertSame(['kalessil/production-dependencies-guard-manifest'], $component->packages());
        $this->assertSame(['manifest'], $component->why('...'));
        putenv('COMPOSER=');
    }
}