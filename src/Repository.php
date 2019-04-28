<?php

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

final class Repository
{
    public function contains(string $dependency): bool {
        return $dependency === 'kalessil/production-dependencies-guard';
    }
}