<?php

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface as ComposerPluginContract;
use Composer\EventDispatcher\EventSubscriberInterface as EventSubscriberContract;

class Guard implements ComposerPluginContract, EventSubscriberContract
{
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return array(
            'pre-install-cmd' => 'methodName',
            'pre-update-cmd'  => 'methodName',
        );
    }
}