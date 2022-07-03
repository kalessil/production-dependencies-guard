<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors;

use Composer\Package\CompletePackageInterface;
use Kalessil\Composer\Plugins\ProductionDependenciesGuard\Inspectors\InspectorInterface as InspectorContract;

final class ByPackageNameInspector implements InspectorContract
{
    private static $vendors = [
        'phpunit/',
        'codeception/',
        'behat/',
        'phpspec/',
        'phpstan/',
    ];

    private static $packages = [
        'kalessil/production-dependencies-guard',
        'roave/security-advisories',
        'sensiolabs/security-checker',
        'mediact/dependency-guard',

        /* PhpUnit-extensions and tooling */
        'johnkary/phpunit-speedtrap',
        'brianium/paratest',
        'mybuilder/phpunit-accelerator',
        'codedungeon/phpunit-result-printer',
        'spatie/phpunit-watcher',
        'satooshi/php-coveralls',

        /* Frameworks components and tooling */
        'symfony/phpunit-bridge',
        'symfony/maker-bundle',
        'zendframework/zend-test',
        'zendframework/zend-debug',
        'yiisoft/yii2-gii',
        'yiisoft/yii2-debug',
        'orchestra/testbench',
        'barryvdh/laravel-debugbar',
        'filp/whoops',
        'nunomaduro/collision',
        'beyondcode/laravel-dump-server',
        'wnx/laravel-stats',
        'insolita/yii2-codestat',
        'nunomaduro/larastan',

        /* dev-tools */
        'humbug/humbug',
        'infection/infection',
        'mockery/mockery',
        'mikey179/vfsstream',
        'phing/phing',

        /* SCA and code quality tools */
        'friendsofphp/php-cs-fixer',
        'vimeo/psalm',
        'jakub-onderka/php-parallel-lint',
        'squizlabs/php_codesniffer',
        'slevomat/coding-standard',
        'doctrine/coding-standard',
        'zendframework/zend-coding-standard',
        'yiisoft/yii2-coding-standards',
        'wp-coding-standards/wpcs',
        'phpcompatibility/php-compatibility',
        'consistence/coding-standard',
        'sylius-labs/coding-standard',
        'phpmd/phpmd',
        'pdepend/pdepend',
        'sebastian/phpcpd',
        'povils/phpmnd',
        'phan/phan',
        'phpro/grumphp',
        'wimg/php-compatibility',
        'sstalle/php7cc',
        'phploc/phploc',
    ];

    private function containsByVendor(string $dependency): bool
    {
        $callback = static function (string $vendor) use ($dependency): bool { return stripos($dependency, $vendor) === 0; };
        return array_filter(self::$vendors, $callback) !== [];
    }

    private function contains(string $dependency): bool
    {
        return \in_array($dependency, self::$packages, true);
    }

    public function canUse(CompletePackageInterface $package): bool
    {
        return ! $this->contains($packageName = strtolower($package->getName())) && ! $this->containsByVendor($packageName);
    }
}