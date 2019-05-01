<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers;

use Composer\Factory;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\SupplierInterface as SuplierContract;

final class FromComposerManifestSupplier implements SuplierContract
{
    public function packages(): array
    {
        $manifest = json_decode(file_get_contents(Factory::getComposerFile()), true);
        return array_keys($manifest['require'] ?? []);
    }
}