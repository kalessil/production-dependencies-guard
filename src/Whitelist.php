<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Package\CompletePackageInterface;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\InspectorInterface as InspectorContract;

final class Whitelist implements InspectorContract
{
    /** @var array<int,string> */
    private $whitelist;

    public function __construct(array $whitelist)
    {
        $this->whitelist = $whitelist;
    }

    public function canUse(CompletePackageInterface $package): bool
    {
        return $this->whitelist !== [] && \in_array(strtolower($package->getName()), $this->whitelist, true);
    }
}