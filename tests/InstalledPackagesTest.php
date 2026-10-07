<?php

declare(strict_types=1);

namespace Shopware\DevTools\Tests;

use Composer\Semver\Semver;
use PHPUnit\Framework\TestCase;

final class InstalledPackagesTest extends TestCase
{
    /** @var array<string, mixed> */
    private static $manifest;

    /** @var array<int, array<string, mixed>> */
    private static $installedPackages;

    public static function setUpBeforeClass(): void
    {
        $root = dirname(__DIR__);
        $json = file_get_contents($root . '/composer.json');
        self::assertNotFalse($json);
        self::$manifest = json_decode($json, true);
        self::assertIsArray(self::$manifest);

        $installedPath = $root . '/vendor/composer/installed.json';
        if (!is_readable($installedPath)) {
            self::fail('vendor/ is missing; run composer update before executing the test suite.');
        }

        $installedJson = file_get_contents($installedPath);
        self::assertNotFalse($installedJson);
        $decoded = json_decode($installedJson, true);
        self::assertIsArray($decoded);
        self::assertArrayHasKey('packages', $decoded);
        self::$installedPackages = $decoded['packages'];
    }

    public function testRootPackageIsThisMetapackage(): void
    {
        $installedPhpPath = dirname(__DIR__) . '/vendor/composer/installed.php';
        self::assertFileExists($installedPhpPath);

        $installed = require $installedPhpPath;
        self::assertIsArray($installed);
        self::assertArrayHasKey('root', $installed);
        self::assertSame('shopware/dev-tools', $installed['root']['name']);
    }

    public function testEachRequiredPackageIsInstalledWithinConstraint(): void
    {
        $byName = [];
        foreach (self::$installedPackages as $package) {
            $byName[$package['name']] = $package;
        }

        foreach (self::$manifest['require'] as $name => $constraint) {
            if ($name === 'php') {
                continue;
            }

            self::assertArrayHasKey($name, $byName, sprintf('Package %s is not installed.', $name));

            $version = $byName[$name]['version'];
            if (strpos($version, 'v') === 0) {
                $version = substr($version, 1);
            }

            self::assertTrue(
                Semver::satisfies($version, $constraint),
                sprintf('Installed %s version %s does not satisfy %s.', $name, $version, $constraint)
            );
        }
    }

    public function testPlatformPhpSatisfiesDeclaredFloor(): void
    {
        $constraint = self::$manifest['require']['php'];
        $version = PHP_VERSION;
        if (strpos($version, 'v') === 0) {
            $version = substr($version, 1);
        }

        self::assertTrue(
            Semver::satisfies($version, $constraint),
            sprintf('PHP %s does not satisfy manifest constraint %s.', $version, $constraint)
        );
    }

    public function testImagesGeneratorReplacesMaltyxxAndKeepsNamespace(): void
    {
        $byName = [];
        foreach (self::$installedPackages as $package) {
            $byName[$package['name']] = $package;
        }

        self::assertArrayHasKey('shopwarelabs/images-generator', $byName);
        $package = $byName['shopwarelabs/images-generator'];

        self::assertArrayHasKey('replace', $package);
        self::assertArrayHasKey('maltyxx/images-generator', $package['replace']);

        self::assertArrayHasKey('autoload', $package);
        self::assertArrayHasKey('psr-4', $package['autoload']);
        self::assertArrayHasKey('Maltyxx\\ImagesGenerator\\', $package['autoload']['psr-4']);
    }
}
