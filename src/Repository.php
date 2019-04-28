<?php

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

final class Repository
{
    private $vendors  = [
        'phpunit/',
        'codeception/',
        'behat/',
        'phpspec/'
    ];

    private $packages = [
        'kalessil/production-dependencies-guard',
        'roave/security-advisories',

        /* PhpUnit-extensions and tooling */
        'johnkary/phpunit-speedtrap',
        'brianium/paratest',
        'mybuilder/phpunit-accelerator',
        'codedungeon/phpunit-result-printer',
        'spatie/phpunit-watcher',
        
        /* Frameworks components and tooling */
        'symfony/phpunit-bridge',
        'symfony/debug',
        'symfony/var-dumper',
        'symfony/maker-bundle',
        'zendframework/zend-test',
        'zendframework/zend-debug',
        'yiisoft/yii2-gii',
        'yiisoft/yii2-debug',
        'orchestra/testbench',
        'barryvdh/laravel-debugbar',

        /* more dev-packages  */
        'humbug/humbug',
        'infection/infection',
        'mockery/mockery',
        'satooshi/php-coveralls',
        'mikey179/vfsStream',
        'filp/whoops',
    ];

    private function containsVendor(string $dependency): bool {
        $callback = static function (string $vendor) use ($dependency): bool { return stripos($dependency, $vendor) === 0; };
        return array_filter($this->vendors, $callback) === [];
    }

    public function contains(string $dependency): bool {
        return \in_array(strtolower($dependency), $this->packages, true) || $this->containsVendor($dependency);
    }
}