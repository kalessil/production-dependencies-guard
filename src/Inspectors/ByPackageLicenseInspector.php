<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\InspectorInterface as InspectorContract;

final class ByPackageLicenseInspector implements InspectorContract
{
    /** @var array<string> */
    private $allowed;

    /**
     * @param array<string> $allowed
     */
    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }

    public function canUse(CompletePackageInterface $package): bool
    {
        $licenses = $package->getLicense();
        $hasLicense = ! empty($licenses);
        if ($hasLicense && $this->allowed !== []) {
            return array_intersect(
                array_map(static function (string $license): string {
                    return strtolower(trim($license));
                }, $licenses),
                $this->allowed
            ) !== [];
        }

        return $hasLicense;
    }
}