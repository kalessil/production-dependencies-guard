<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Package\CompletePackageInterface;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\InspectorInterface as InspectorContract;

final class Whitelist implements InspectorContract
{
    /** @var array<int,string> */
    private $whitelist;

    public function __construct(array $settings)
    {
        $this->whitelist = array_map(
            static function (string $setting): string { return str_replace('white-list:', '', $setting); },
            array_filter(
                array_map('strtolower', array_map('trim', $settings)),
                static function (string $setting): bool { return strncmp($setting, 'white-list:', 11) === 0; }
            )
        );
    }

    public function canUse(CompletePackageInterface $package): bool
    {
        return $this->whitelist !== [] && \in_array(strtolower($package->getName()), $this->whitelist, true);
    }
}