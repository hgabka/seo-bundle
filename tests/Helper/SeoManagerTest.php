<?php

namespace Hgabka\SeoBundle\Tests\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Hgabka\SeoBundle\Entity\Seo;
use Hgabka\SeoBundle\Helper\SeoManager;
use Hgabka\SeoBundle\Repository\SeoRepository;
use Hgabka\SeoBundle\Tests\Fixtures\TestPage;
use Hgabka\UtilsBundle\Helper\HgabkaUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SeoManagerTest extends TestCase
{
    private ?Seo $general = null;

    private ?Seo $pageSeo = null;

    private int $findCalls = 0;

    private string $projectDir;

    protected function setUp(): void
    {
        $this->projectDir = sys_get_temp_dir() . '/hg_seo_' . bin2hex(random_bytes(4));
        mkdir($this->projectDir . '/public', 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->projectDir . '/public/*') ?: [] as $file) {
            unlink($file);
        }
        @rmdir($this->projectDir . '/public');
        @rmdir($this->projectDir);
    }

    private function manager(string $websiteTitle = 'My Site'): SeoManager
    {
        $repository = $this->createStub(SeoRepository::class);
        $repository->method('findGeneral')->willReturnCallback(fn (): ?Seo => $this->general);
        $repository->method('findOrCreateFor')->willReturnCallback(function ($entity): Seo {
            ++$this->findCalls;

            return $this->pageSeo ?? (new Seo())->setRef($entity);
        });

        $metadata = $this->createStub(ClassMetadata::class);
        $metadata->method('getFieldNames')->willReturn(['id', 'metaTitle', 'metaDescription', 'ogTitle', 'metaRobots']);

        $em = $this->createStub(EntityManagerInterface::class);
        $em->method('getRepository')->willReturn($repository);
        $em->method('getClassMetadata')->willReturn($metadata);

        $utils = $this->createStub(HgabkaUtils::class);
        $utils->method('getSchemeAndHttpHost')->willReturn('https://example.test');

        return new SeoManager($em, $utils, $websiteTitle, $this->projectDir);
    }

    public function testSeoOfAPageInheritsEmptyFieldsFromTheGeneralSeo(): void
    {
        $this->general = (new Seo())->setMetaTitle('General')->setMetaDescription('General description')->setMetaRobots('index');
        $this->pageSeo = (new Seo())->setMetaDescription('Page description');

        $seo = $this->manager()->getSeoFor(new TestPage(1, 'Page'));

        $this->assertSame('Page description', $seo->getMetaDescription());
        $this->assertSame('General', $seo->getMetaTitle());
        $this->assertSame('index', $seo->getMetaRobots());
    }

    public function testGeneralImagesAreInheritedToo(): void
    {
        $this->general = (new Seo())->setOgImage($og = new \Hgabka\MediaBundle\Entity\Media())->setTwitterImage($tw = new \Hgabka\MediaBundle\Entity\Media());
        $this->pageSeo = new Seo();

        $seo = $this->manager()->getSeoFor(new TestPage(1));

        $this->assertSame($og, $seo->getOgImage());
        $this->assertSame($tw, $seo->getTwitterImage());
    }

    public function testSeoIsLoadedOncePerPage(): void
    {
        $manager = $this->manager();
        $page = new TestPage(1);

        $first = $manager->getSeoFor($page);

        $this->assertSame($first, $manager->getSeoFor($page));
        $this->assertSame(1, $this->findCalls);
        $manager->getSeoFor(new TestPage(2));
        $this->assertSame(2, $this->findCalls);
    }

    public function testTitleForPrefersSeoTitleThenPageTitle(): void
    {
        $this->pageSeo = (new Seo())->setMetaTitle('SEO title');
        $this->assertSame('SEO title', $this->manager()->getTitleFor(new TestPage(1, 'Page title')));

        $this->pageSeo = new Seo();
        $this->assertSame('Page title', $this->manager()->getTitleFor(new TestPage(2, 'Page title')));

        $this->assertSame('', $this->manager()->getTitleFor(new TestPage(3)));
    }

    public function testWebsiteTitlePlaceholderIsExpanded(): void
    {
        $this->pageSeo = (new Seo())->setMetaTitle('Contact | %websitetitle%');

        $this->assertSame('Contact | Acme', $this->manager('Acme')->getTitleFor(new TestPage(1)));
    }

    public function testGeneralTitle(): void
    {
        $this->assertSame('', $this->manager()->getTitle());

        $this->general = (new Seo())->setMetaTitle('%websitetitle% home');
        $this->assertSame('My Site home', $this->manager()->getTitle());
    }

    public function testTitleForPageOrDefaultOrder(): void
    {
        $this->pageSeo = new Seo();
        $manager = $this->manager();

        $this->assertSame('Default', $manager->getTitleForPageOrDefault(null, 'Default'));
        $this->assertSame('Default', $manager->getTitleForPageOrDefault(new TestPage(1, 'Page'), 'Default'));
        $this->assertSame('Page', $manager->getTitleForPageOrDefault(new TestPage(2, 'Page')));

        $this->pageSeo = (new Seo())->setMetaTitle('SEO');
        $this->assertSame('SEO', $this->manager()->getTitleForPageOrDefault(new TestPage(3, 'Page'), 'Default'));
    }

    #[DataProvider('urls')]
    public function testAbsoluteUrl(string $url, ?string $host, string|false $expected): void
    {
        $this->assertSame($expected, $this->manager()->getAbsoluteUrl($url, $host));
    }

    public static function urls(): array
    {
        return [
            'already absolute' => ['https://example.com/a', 'https://host.test', 'https://example.com/a'],
            'root relative' => ['/img/a.png', 'https://host.test', 'https://host.test/img/a.png'],
            'host trailing slash' => ['/img/a.png', 'https://host.test/', 'https://host.test/img/a.png'],
            'relative path' => ['img/a.png', 'https://host.test', false],
        ];
    }

    public function testImageDimensionsOfLocalFile(): void
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAADCAYAAAC56t6BAAAAEElEQVR4nGNgYGD4z4AGAAARAgEAf2cmSgAAAABJRU5ErkJggg==');
        file_put_contents($this->projectDir . '/public/a.png', $png);

        $this->assertSame(['width' => 2, 'height' => 3], $this->manager()->getImageDimensions($this->projectDir . '/public/a.png'));
    }

    public function testImageDimensionsOfSameHostUrlResolveToPublicDir(): void
    {
        file_put_contents($this->projectDir . '/public/a.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAIAAAADCAYAAAC56t6BAAAAEElEQVR4nGNgYGD4z4AGAAARAgEAf2cmSgAAAABJRU5ErkJggg=='));

        $this->assertSame(['width' => 2, 'height' => 3], $this->manager()->getImageDimensions('https://example.test/a.png'));
    }

    public function testMissingSameHostFileNeverTriggersAnHttpRequest(): void
    {
        $this->assertNull($this->manager()->getImageDimensions('https://example.test/missing.png'));
    }

    public function testNonImageFileHasNoDimensions(): void
    {
        file_put_contents($this->projectDir . '/public/a.txt', 'not an image');

        $this->assertNull($this->manager()->getImageDimensions($this->projectDir . '/public/a.txt'));
    }
}
