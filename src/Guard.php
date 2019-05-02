<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface as EventSubscriberContract;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Plugin\PluginInterface as ComposerPluginContract;
use Composer\Script\ScriptEvents;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageAbandonedInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageDescriptionInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageLicenseInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageNameInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageTypeInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\StubInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\FromComposerLockSupplier;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\FromComposerManifestSupplier;

final class Guard implements ComposerPluginContract, EventSubscriberContract
{
    const CHECK_LOCK_FILE    = 'check-lock-file';
    const CHECK_DESCRIPTION  = 'check-description';
    const CHECK_LICENSE      = 'check-license';
    const CHECK_ABANDONED    = 'check-abandoned';

    /** @var bool */
    private $useLockFile;
    /** @var Composer */
    private $composer;
    /** @var array<string,InspectorContract> */
    private $inspectors;
    /** @var Whitelist */
    private $whitelist;

    public function activate(Composer $composer, IOInterface $io)
    {
        $manifest = json_decode(file_get_contents(Factory::getComposerFile()), true);
        $settings = $manifest['extra']['production-dependencies-guard'] ?? [];

        $checkLicense     = \in_array(self::CHECK_LICENSE,     $settings, true);
        $checkAbandoned   = \in_array(self::CHECK_ABANDONED,   $settings, true);
        $checkDescription = \in_array(self::CHECK_DESCRIPTION, $settings, true);
        $this->inspectors = [
            'dev-package-name'     => new ByPackageNameInspector(),
            'dev-package-type'     => new ByPackageTypeInspector(),
            'missing-license'      => $checkLicense     ? new ByPackageLicenseInspector($settings) : new StubInspector(),
            'abandoned'            => $checkAbandoned   ? new ByPackageAbandonedInspector()        : new StubInspector(),
            'description-keywords' => $checkDescription ? new ByPackageDescriptionInspector()      : new StubInspector(),
        ];

        $this->composer    = $composer;
        $this->whitelist   = new Whitelist($settings);
        $this->useLockFile = \in_array(self::CHECK_LOCK_FILE, $settings, true);
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            ScriptEvents::PRE_INSTALL_CMD  => ['checkManifest'],
            ScriptEvents::PRE_UPDATE_CMD   => ['checkManifest'],
            ScriptEvents::POST_INSTALL_CMD => ['checkGeneric'],
            ScriptEvents::POST_UPDATE_CMD  => ['checkGeneric'],
        );
    }

    private function check(CompletePackageInterface... $packages)
    {
        $violations = [];
        foreach ($packages as $package) {
            $violations[$packageName = $package->getName()] = [];
            foreach ($this->inspectors as $rule => $inspector) {
                if (! $inspector->canUse($package)) {
                    $violations[$packageName] []= $rule;
                }
            }
        }
        if (($violations = array_filter($violations)) !== []) {
            $message = sprintf(
                'Dependencies guard has found violations in require-dependencies (source: %s):',
                $this->useLockFile ? 'lock-file' : 'manifest'
            );
            foreach ($violations as $packageName => $rules) {
                $message .= PHP_EOL . ' - ' . $packageName . ': ' . implode(', ', $rules);
            }
            throw new \RuntimeException($message);
        }
    }

    /** @return array<int, CompletePackageInterface> */
    private function find(string... $packages): array {
        return array_filter(
            array_filter(
                $this->composer->getRepositoryManager()->getLocalRepository()->getPackages(),
                static function (CompletePackageInterface $package) use ($packages): bool { return \in_array($package->getName(), $packages, true); }
            ),
            function (CompletePackageInterface $package): bool { return ! $this->whitelist->canUse($package); }
        );
    }

    public function checkManifest()
    {
        $supplier = new FromComposerManifestSupplier();
        $this->check(...$this->find(...$supplier->packages()));
    }

    public function checkGeneric()
    {
        $supplier = $this->useLockFile ? new FromComposerLockSupplier() : new FromComposerManifestSupplier();
        $this->check(...$this->find(...$supplier->packages()));
    }
}