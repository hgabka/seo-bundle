<?php

namespace Hgabka\SeoBundle\Tests\Twig;

use Doctrine\ORM\EntityManager;
use Hgabka\SeoBundle\Entity\Seo;
use Hgabka\SeoBundle\Helper\SeoManager;
use Hgabka\SeoBundle\Tests\Fixtures\TestPage;
use Hgabka\SeoBundle\Twig\SeoTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class SeoTwigExtensionTest extends TestCase
{
    private function twig(): Environment
    {
        return new Environment(new ArrayLoader([
            'meta' => '{{ seo.metaTitle }}|{{ entity.title|default("-") }}|{{ currentNode|default("no-node") }}',
            'general' => '{{ seo is empty ? "none" : seo.metaTitle }}',
        ]));
    }

    public function testRegisteredFunctions(): void
    {
        $extension = new SeoTwigExtension($this->createStub(EntityManager::class), $this->createStub(SeoManager::class));

        $this->assertSame(
            ['render_seo_metadata_for', 'render_general_seo_metadata', 'get_seo_for', 'get_title', 'get_title_for', 'get_title_for_page_or_default', 'get_absolute_url', 'get_image_dimensions'],
            array_map(static fn ($f) => $f->getName(), $extension->getFunctions())
        );
    }

    public function testRenderSeoMetadataForPassesSeoEntityAndNode(): void
    {
        $page = new TestPage(1, 'Page');
        $manager = $this->createMock(SeoManager::class);
        $manager->expects($this->once())->method('getSeoFor')->with($page)->willReturn((new Seo())->setMetaTitle('SEO'));
        $extension = new SeoTwigExtension($this->createStub(EntityManager::class), $manager);

        $this->assertSame('SEO|Page|N1', $extension->renderSeoMetadataFor($this->twig(), $page, 'N1', 'meta'));
        $manager2 = $this->createStub(SeoManager::class);
        $manager2->method('getSeoFor')->willReturn(new Seo());
        $this->assertSame('|Page|no-node', (new SeoTwigExtension($this->createStub(EntityManager::class), $manager2))->renderSeoMetadataFor($this->twig(), $page, null, 'meta'));
    }

    public function testRenderGeneralSeoMetadata(): void
    {
        $manager = $this->createStub(SeoManager::class);
        $manager->method('getGeneralSeo')->willReturn((new Seo())->setMetaTitle('General'));

        $this->assertSame('General', (new SeoTwigExtension($this->createStub(EntityManager::class), $manager))->renderGeneralSeoMetadata($this->twig(), 'general'));
    }

    public function testHelpersDelegateToTheManager(): void
    {
        $page = new TestPage(1, 'Page');
        $manager = $this->createMock(SeoManager::class);
        $manager->expects($this->once())->method('getTitleFor')->with($page)->willReturn('T');
        $manager->expects($this->once())->method('getTitle')->willReturn('General');
        $manager->expects($this->once())->method('getTitleForPageOrDefault')->with($page, 'D')->willReturn('TD');
        $manager->expects($this->once())->method('getAbsoluteUrl')->with('/a', 'https://h')->willReturn('https://h/a');
        $manager->expects($this->once())->method('getImageDimensions')->with('/img.png')->willReturn(['width' => 1, 'height' => 2]);

        $extension = new SeoTwigExtension($this->createStub(EntityManager::class), $manager);

        $this->assertSame('T', $extension->getTitleFor($page));
        $this->assertSame('General', $extension->getTitle());
        $this->assertSame('TD', $extension->getTitleForPageOrDefault($page, 'D'));
        $this->assertSame('https://h/a', $extension->getAbsoluteUrl('/a', 'https://h'));
        $this->assertSame(['width' => 1, 'height' => 2], $extension->getImageDimensions('/img.png'));
    }
}
