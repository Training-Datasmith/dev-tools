<?php

declare(strict_types=1);

namespace Shopware\DevTools\Tests;

use PHPUnit\Framework\TestCase;

final class ComposerManifestTest extends TestCase
{
    /** @var array<string, mixed> */
    private static $manifest;

    /** @var string */
    private static $readme;

    public static function setUpBeforeClass(): void
    {
        $root = dirname(__DIR__);
        $json = file_get_contents($root . '/composer.json');
        self::assertNotFalse($json);
        self::$manifest = json_decode($json, true);
        self::assertIsArray(self::$manifest);

        $readme = file_get_contents($root . '/README.md');
        self::assertNotFalse($readme);
        self::$readme = $readme;
    }

    public function testPackageNameAndLicense(): void
    {
        self::assertSame('shopware/dev-tools', self::$manifest['name']);
        self::assertSame('MIT', self::$manifest['license']);
    }

    public function testRequireConstraintsMatchContract(): void
    {
        self::assertArrayHasKey('require', self::$manifest);
        $expected = [
            'php' => '^7.4 | ^8.0',
            'doctrine/sql-formatter' => '^1.1',
            'fakerphp/faker' => '^1.20',
            'shopwarelabs/images-generator' => '^1.0.0',
            'mbezhanov/faker-provider-collection' => '^2.0.1',
            'symfony/doctrine-bridge' => '^5.4.7 | ^6 | ^7',
            'symfony/web-profiler-bundle' => '^5.4 | ^6 | ^7',
            'phpunit/phpunit' => '^9.6 | ^10 | ^11.4 | ^12',
            'symfony/browser-kit' => '^5.4 | ^6 | ^7',
        ];

        foreach ($expected as $package => $constraint) {
            self::assertArrayHasKey($package, self::$manifest['require']);
            self::assertSame($constraint, self::$manifest['require'][$package]);
        }

        self::assertCount(count($expected), self::$manifest['require']);
    }

    public function testDeadImagesPackageIsNotRequired(): void
    {
        self::assertArrayNotHasKey('maltyxx/images-generator', self::$manifest['require']);
    }

    public function testProductionAutoloadIsAbsent(): void
    {
        self::assertArrayNotHasKey('autoload', self::$manifest);
    }

    public function testReadmeDocumentsDevInstall(): void
    {
        self::assertStringContainsString('composer require --dev shopware/dev-tools', self::$readme);
    }
}
