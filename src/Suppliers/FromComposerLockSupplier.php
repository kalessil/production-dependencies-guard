<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers;

use Composer\Factory;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\SupplierInterface as SuplierContract;

final class FromComposerLockSupplier implements SuplierContract
{
    public function packages(): array
    {
        $manifest = json_decode(file_get_contents(substr(Factory::getComposerFile(), 0, -5) . '.lock'), true);
        return array_column($manifest['packages'] ?? [], 'name');
    }
}