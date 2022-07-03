<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\InspectorInterface as InspectorContract;

final class ByPackageDescriptionInspector implements InspectorContract
{
    /* ignore reason: see https://github.com/symfony/symfony/issues/31379 */
    const IGNORE_PACKAGES = [
        'symfony/debug',
        'symfony/var-dumper',
        'symfony/error-handler',
    ];

    private function hasDebugKeyword(CompletePackageInterface $package): bool
    {
        $callback = static function (string $term): bool { return strtolower($term) === 'debug'; };
        return array_filter($package->getKeywords() ?: [], $callback) !== [];
    }

    private function hasAnalyzerDescription(CompletePackageInterface $package): bool
    {
        $description = $package->getDescription() ?: '';
        return stripos($description, 'debug') !== false || preg_match('/static\s+(code\s+)?(analyzer|analysis)/i', $description) === 1;
    }

    public function canUse(CompletePackageInterface $package): bool
    {
        if (true === in_array(strtolower($package->getName()), self::IGNORE_PACKAGES, true)) {
            return true;
        }

        return ! $this->hasDebugKeyword($package) && ! $this->hasAnalyzerDescription($package);
    }
}