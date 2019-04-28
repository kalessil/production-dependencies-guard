<?php

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface as ComposerPluginContract;
use Composer\EventDispatcher\EventSubscriberInterface as EventSubscriberContract;
use Composer\Script\ScriptEvents;

final class Guard implements ComposerPluginContract, EventSubscriberContract
{
    /** @var \Kalessil\Composer\Plugins\ProductionDependenciesGuard\Repository */
    private $repository;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->repository = new Repository();
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => array('check'),
            ScriptEvents::POST_UPDATE_CMD => array('check'),
        );
    }

    public function check() {
        $manifest   = json_decode(file_get_contents(\Composer\Factory::getComposerFile()), true);
        $violations = array_filter(array_keys($manifest['require'] ?? []), [$this->repository, 'contains']);
        if ($violations !== []) {
            $message = sprintf('Following dev-dependencies has been found in require-section: %s', implode(', ', $violations));
            trigger_error($message, E_USER_ERROR);
        }
    }
}