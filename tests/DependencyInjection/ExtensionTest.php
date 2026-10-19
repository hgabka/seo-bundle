<?php

namespace Hgabka\SeoBundle\Tests\DependencyInjection;

use Hgabka\SeoBundle\DependencyInjection\HgabkaSeoExtension;
use Hgabka\SeoBundle\Helper\SeoManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionTest extends TestCase
{
    public function testServicesAndParametersAreLoaded(): void
    {
        $container = new ContainerBuilder();

        (new HgabkaSeoExtension())->load([], $container);

        $this->assertTrue($container->hasDefinition(SeoManager::class));
        $this->assertSame('', $container->getParameter('google.analytics.account_id'));
        $this->assertSame('User-agent: *', $container->getParameter('robots_default'));
        $this->assertArrayHasKey('twig.extension', $container->getDefinition('hgabka_seo.twig.extension')->getTags());
        $this->assertSame('hgabka_node.adaptForm', $container->getDefinition('hgabka_seo.node.listener')->getTag('kernel.event_listener')[0]['event']);
    }

    public function testImagineFiltersArePrepended(): void
    {
        $container = new ContainerBuilder();

        (new HgabkaSeoExtension())->prepend($container);

        $config = $container->getExtensionConfig('liip_imagine');
        $this->assertNotEmpty($config);
        $this->assertArrayHasKey('filter_sets', $config[0]);
    }
}
