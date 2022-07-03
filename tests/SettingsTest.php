<?php declare(strict_types=1);

namespace Kalessil\Composer\Plugins\ProductionDependenciesGuard;

use PHPUnit\Framework\TestCase;

final class SettingsTest extends TestCase
{
    public function testActivateAdditionalFeatures(): void
    {
        putenv(sprintf('COMPOSER=%s/data/for-settings-test.json', __DIR__));

        $settings = new Settings();

        $expectedWhiteList = [
            'vendor/trim',
            'vendor/capitalization',
        ];

        $expectedAcceptLicense = [
            'trim',
            'capitalization',
        ];

        $expectedPackageGuards = [
            'vendor/package1' => [ 'abandoned' ],
            'vendor/package2' => [ 'description', 'license' ],
        ];

        $this->assertTrue($settings->checkAbandoned());
        $this->assertTrue($settings->checkDescription());
        $this->assertTrue($settings->checkLicense());
        $this->assertTrue($settings->checkLockFile());
        $this->assertSame($expectedWhiteList, $settings->whiteList());
        $this->assertSame($expectedAcceptLicense, $settings->acceptLicense());
        $this->assertSame($expectedPackageGuards, $settings->packageGuards());
    }

    public function testActivateNoneFeatures(): void
    {
        putenv(sprintf('COMPOSER=%s/data/activate-none-features.json', __DIR__));

        $settings = new Settings();

        $this->assertFalse($settings->checkAbandoned());
        $this->assertFalse($settings->checkDescription());
        $this->assertFalse($settings->checkLicense());
        $this->assertFalse($settings->checkLockFile());
        $this->assertEmpty($settings->whiteList());
        $this->assertEmpty($settings->acceptLicense());
        $this->assertEmpty($settings->packageGuards());
    }

    public function testMalformedBoolean(): void
    {
        putenv(sprintf('COMPOSER=%s/data/for-settings-test-malformed-boolean.json', __DIR__));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Malformed setting, found unexpected colon: check-description');

        new Settings();
    }

    public function testMalformedList(): void
    {
        putenv(sprintf('COMPOSER=%s/data/for-settings-test-malformed-list.json', __DIR__));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Malformed setting, expected only one colon: white-list');

        new Settings();
    }

    public function testMalformedOption(): void
    {
        putenv(sprintf('COMPOSER=%s/data/for-settings-test-malformed-option.json', __DIR__));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Malformed setting, expected two colons: package-guards');

        new Settings();
    }

    public function testUnknownSetting(): void
    {
        putenv(sprintf('COMPOSER=%s/data/for-settings-test-unknown-setting.json', __DIR__));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown setting: foobar');

        new Settings();
    }
}