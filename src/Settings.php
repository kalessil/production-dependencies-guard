<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use Composer\Factory;

class Settings
{
    const CHECK_ABANDONED = 'check-abandoned';
    const CHECK_DESCRIPTION = 'check-description';
    const CHECK_LICENSE = 'check-license';
    const CHECK_LOCK_FILE = 'check-lock-file';
    const WHITE_LIST = 'white-list';
    const ACCEPT_LICENSE = 'accept-license';
    const PACKAGE_GUARDS = 'package-guards';

    const BOOLEAN_SETTINGS = [
        self::CHECK_ABANDONED,
        self::CHECK_DESCRIPTION,
        self::CHECK_LICENSE,
        self::CHECK_LOCK_FILE,
    ];

    const LIST_SETTINGS = [
        self::WHITE_LIST,
        self::ACCEPT_LICENSE,
    ];

    const OPTION_SETTINGS = [
        self::PACKAGE_GUARDS,
    ];

    /** @var array */
    private $settings;

    public function __construct()
    {
        $manifest = json_decode(file_get_contents(Factory::getComposerFile()), true);
        $manifestSettings = $manifest['extra']['production-dependencies-guard'] ?? [];

        foreach ($manifestSettings as $setting) {
            $settingParts = explode(':', $setting);
            $settingName = strtolower(trim(array_shift($settingParts)));

            if (true === in_array($settingName, self::BOOLEAN_SETTINGS, true)) {
                if (count($settingParts) !== 0) {
                    throw new \InvalidArgumentException('Malformed setting, found unexpected colon: ' . $settingName);
                }

                $this->settings[$settingName] = true;
            } elseif (true === in_array($settingName, self::LIST_SETTINGS, true)) {
                if (count($settingParts) !== 1) {
                    throw new \InvalidArgumentException('Malformed setting, expected only one colon: ' . $settingName);
                }

                $this->settings[$settingName][] = strtolower(trim($settingParts[0]));
            } elseif (true === in_array($settingName, self::OPTION_SETTINGS, true)) {
                if (count($settingParts) !== 2) {
                    throw new \InvalidArgumentException('Malformed setting, expected two colons: ' . $settingName);
                }

                $this->settings[$settingName][strtolower(trim($settingParts[0]))] = array_map(static function (string $guard): string {
                    return strtolower(trim($guard));
                }, explode(',', $settingParts[1]));
            } else {
                throw new \InvalidArgumentException('Unknown setting: ' . $settingName);
            }
        }
    }

    public function checkAbandoned(): bool
    {
        return $this->settings[self::CHECK_ABANDONED] ?? false;
    }

    public function checkDescription(): bool
    {
        return $this->settings[self::CHECK_DESCRIPTION] ?? false;
    }

    public function checkLicense(): bool
    {
        return $this->settings[self::CHECK_LICENSE] ?? false;
    }

    public function checkLockFile(): bool
    {
        return $this->settings[self::CHECK_LOCK_FILE] ?? false;
    }

    /** @return array<string> */
    public function whiteList(): array
    {
        return $this->settings[self::WHITE_LIST] ?? [];
    }

    /** @return array<string> */
    public function acceptLicense(): array
    {
        return $this->settings[self::ACCEPT_LICENSE] ?? [];
    }

    /** @return array<string, array<string>> */
    public function packageGuards(): array
    {
        return $this->settings[self::PACKAGE_GUARDS] ?? [];
    }
}
