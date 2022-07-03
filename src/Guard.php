<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface as EventSubscriberContract;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Plugin\PluginInterface as ComposerPluginContract;
use Composer\Script\ScriptEvents;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageAbandonedInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageDescriptionInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageLicenseInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageNameInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\ByPackageTypeInspector;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\InspectorInterface as InspectorContract;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\FromComposerLockSupplier;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\FromComposerManifestSupplier;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Suppliers\SupplierInterface as SupplierContract;

final class Guard implements ComposerPluginContract, EventSubscriberContract
{
    /** @var bool */
    private $useLockFile;
    /** @var Composer */
    private $composer;
    /** @var array<string,InspectorContract> */
    private $inspectors;
    /** @var Whitelist */
    private $whitelist;
    /** @var SupplierContract */
    private $supplier;
    /** @var array<string, array<string>> */
    private $packageInspectors;

    public function activate(Composer $composer, IOInterface $io)
    {
        $settings = new Settings();

        $this->inspectors = [
            'dev-package-name' => new ByPackageNameInspector(),
            'dev-package-type' => new ByPackageTypeInspector(),
        ];

        if ($settings->checkLicense()) {
            $this->inspectors['license'] = new ByPackageLicenseInspector($settings->acceptLicense());
        }

        if ($settings->checkAbandoned()) {
            $this->inspectors['abandoned'] = new ByPackageAbandonedInspector();
        }

        if ($settings->checkDescription()) {
            $this->inspectors['description'] = new ByPackageDescriptionInspector();
        }

        $this->packageInspectors = $settings->packageGuards();

        $this->composer    = $composer;
        $this->whitelist   = new Whitelist($settings->whiteList());
        $this->useLockFile = $settings->checkLockFile();
        $this->supplier    = $this->useLockFile ? new FromComposerLockSupplier() : new FromComposerManifestSupplier();
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => ['checkGeneric'],
            ScriptEvents::POST_UPDATE_CMD  => ['checkGeneric'],
        );
    }

    private function check(SupplierContract $supplier, CompletePackageInterface... $packages)
    {
        $violations = [];
        foreach ($packages as $package) {
            $packageName            = strtolower($package->getName());
            $packageId              = sprintf('%s (via %s)', $packageName, implode(', ', $supplier->why($packageName)));
            $violations[$packageId] = [];
            foreach ($this->inspectors as $rule => $inspector) {
                if (! isset($this->packageInspectors[$packageName]) || in_array($rule, $this->packageInspectors[$packageName], true)) {
                    if (! $inspector->canUse($package)) {
                        $violations[$packageId][] = $rule;
                    }
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
    private function find(string ...$packages): array {
        /* @infection-ignore-all */
        return array_filter(
            array_filter(
                $this->composer->getRepositoryManager()->getLocalRepository()->getPackages(),
                static function (CompletePackageInterface $package) use ($packages): bool { return \in_array(strtolower($package->getName()), $packages, true); }
            ),
            function (CompletePackageInterface $package): bool { return ! $this->whitelist->canUse($package); }
        );
    }

    public function checkGeneric()
    {
        $this->check($this->supplier, ...$this->find(...$this->supplier->packages()));
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }
}