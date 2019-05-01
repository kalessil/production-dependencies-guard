<?php declare(strict_types = 1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface;

interface InspectorInterface
{
    public function canUse(CompletePackageInterface $package): bool;
}