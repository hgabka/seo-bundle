<?php

namespace Hgabka\SeoBundle\Tests\Twig;

use Hgabka\SeoBundle\Helper\Order;
use Hgabka\SeoBundle\Helper\OrderConverter;
use Hgabka\SeoBundle\Helper\OrderItem;
use Hgabka\SeoBundle\Helper\OrderPreparer;
use Hgabka\SeoBundle\Twig\GoogleAnalyticsTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class GoogleAnalyticsTwigExtensionTest extends TestCase
{
    private function twig(bool $debug = false): Environment
    {
        $loader = new FilesystemLoader();
        $loader->addPath(__DIR__ . '/../../src/Resources/views', 'HgabkaSeo');
        $twig = new Environment($loader);
        $twig->addGlobal('app', ['debug' => $debug]);

        return $twig;
    }

    public function testFunctionsAreRegistered(): void
    {
        $names = array_map(static fn ($f) => $f->getName(), (new GoogleAnalyticsTwigExtension())->getFunctions());

        $this->assertSame(['google_analytics_initialize', 'google_analytics_track_order'], $names);
    }

    public function testInitializeUsesTheConfiguredAccount(): void
    {
        $extension = new GoogleAnalyticsTwigExtension();
        $extension->setAccountID('UA-1-1');

        $html = $extension->renderInitialize($this->twig());

        $this->assertStringContainsString("_gaq.push(['_setAccount', 'UA-1-1']);", $html);
        $this->assertStringContainsString('googleAnalyticsApi.init(false);', $html);
    }

    public function testInitializeAcceptsOptionsAndDebugMode(): void
    {
        $extension = new GoogleAnalyticsTwigExtension();
        $extension->setAccountID('UA-1-1');

        $html = $extension->renderInitialize($this->twig(true), ['account_id' => 'UA-9-9', 'domain' => 'example.com']);

        $this->assertStringContainsString("'UA-9-9'", $html);
        $this->assertStringContainsString("_gaq.push(['_setDomainName', 'example.com']);", $html);
        $this->assertStringContainsString('googleAnalyticsApi.init(true);', $html);
    }

    public function testEcommerceTrackingUsesThePreparerAndTheConverter(): void
    {
        $extension = new GoogleAnalyticsTwigExtension();
        $extension->setAccountID('UA-1-1');
        $extension->setOrderPreparer(new OrderPreparer());
        $extension->setOrderConverter(new OrderConverter());
        $order = (new Order())->setTransactionID('T1')->setStoreName('Shop');
        $order->orderItems[] = (new OrderItem())->setSKU('A')->setName('Item')->setUnitPrice(10)->setQuantity(1)->setTaxes(2);
        $order->orderItems[] = (new OrderItem())->setSKU('A')->setName('Item')->setUnitPrice(10)->setQuantity(2)->setTaxes(4);

        $html = $extension->renderECommerceTracking($this->twig(), $order);

        $this->assertStringContainsString("'T1'", $html);
        $this->assertStringContainsString("'Shop'", $html);
        $this->assertSame(1, substr_count($html, '_addItem'), 'items with the same SKU are merged');
    }
}
