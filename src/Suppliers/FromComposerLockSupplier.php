<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers;

use Composer\Factory;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\SupplierInterface as SuplierContract;

final class FromComposerLockSupplier implements SuplierContract
{
    /** @var array<string,array<int,string>> */
    private $dependencies = [];

    public function packages(): array
    {
        $manifest = json_decode(file_get_contents(substr(Factory::getComposerFile(), 0, -5) . '.lock'), true);
        foreach ($packages = $manifest['packages'] ?? [] as $package) {
            $packageName                      = strtolower($package['name'] ?? 'unknown');
            $this->dependencies[$packageName] = array_map('strtolower', array_keys($package['require'] ?? []));
        }
        return array_map('strtolower', array_column($packages, 'name'));
    }

    /** @return array<int, string> */
    public function why(string $package): array
    {
        $which = array_filter(
            $this->dependencies,
            static function (array $packages) use ($package): bool { return \in_array($package, $packages, true); }
        );
        return $which === [] ? ['manifest'] : array_keys($which);
    }
}