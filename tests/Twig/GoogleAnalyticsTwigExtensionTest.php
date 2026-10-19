<?php

namespace Hgabka\SeoBundle\Tests\Twig;

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
}
