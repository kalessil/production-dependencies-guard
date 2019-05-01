<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers;

interface SupplierInterface
{
    /** @return array<int, string> */
    public function packages(): array;
}