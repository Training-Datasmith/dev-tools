<?php

declare(strict_types=1);

namespace Shopware\DevTools\Tests;

use Bezhanov\Faker\ProviderCollectionHelper;
use Doctrine\SqlFormatter\SqlFormatter;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CuratedStackSmokeTest extends TestCase
{
    public function testSqlFormatterCompressesAndFormatsSql(): void
    {
        $formatter = new SqlFormatter();

        $compressed = $formatter->compress('select   1');
        self::assertIsString($compressed);
        self::assertStringNotContainsString('  ', $compressed);
        self::assertSame(1, preg_match('/^(select|SELECT) 1$/', trim($compressed)));

        $formatted = $formatter->format('select 1');
        self::assertIsString($formatted);

        $plain = preg_replace('/\x1b\[[\d;]*m/', '', $formatted);
        self::assertIsString($plain);
        self::assertStringContainsString("\n", $plain);
        self::assertNotSame('select 1', trim($plain));
    }

    public function testFakerProviderCollectionRegistersCommerce(): void
    {
        $faker = Factory::create();
        $faker->seed(1);
        ProviderCollectionHelper::addAllProvidersTo($faker);

        $department = $faker->department();
        $productName = $faker->productName();

        self::assertIsString($department);
        self::assertNotSame('', $department);
        self::assertIsString($productName);
        self::assertNotSame('', $productName);
    }

    public function testImagesGeneratorProviderIsLoadableWithoutRendering(): void
    {
        self::assertTrue(class_exists('Maltyxx\\ImagesGenerator\\ImagesGeneratorProvider'));

        $reflection = new ReflectionClass('Maltyxx\\ImagesGenerator\\ImagesGeneratorProvider');
        self::assertTrue($reflection->hasMethod('imageGenerator'));
        self::assertTrue($reflection->getMethod('imageGenerator')->isPublic());
        self::assertTrue($reflection->getMethod('imageGenerator')->isStatic());
    }

    public function testDoctrineBridgeEventManagerIsLoadable(): void
    {
        self::assertTrue(class_exists('Symfony\\Bridge\\Doctrine\\ContainerAwareEventManager'));
    }

    public function testWebProfilerBundleClassIsLoadable(): void
    {
        self::assertTrue(class_exists('Symfony\\Bundle\\WebProfilerBundle\\WebProfilerBundle'));
    }

    public function testBrowserKitAbstractBrowserIsLoadable(): void
    {
        self::assertTrue(class_exists('Symfony\\Component\\BrowserKit\\AbstractBrowser'));

        $reflection = new ReflectionClass('Symfony\\Component\\BrowserKit\\AbstractBrowser');
        self::assertTrue($reflection->isAbstract());
    }
}
