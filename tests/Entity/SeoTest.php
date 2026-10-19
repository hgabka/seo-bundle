<?php

namespace Hgabka\SeoBundle\Tests\Entity;

use Hgabka\MediaBundle\Entity\Media;
use Hgabka\SeoBundle\Entity\Seo;
use Hgabka\SeoBundle\Form\SeoType;
use Hgabka\SeoBundle\Tests\Fixtures\TestPage;
use PHPUnit\Framework\TestCase;

class SeoTest extends TestCase
{
    public function testScalarFieldsRoundTripAndAreFluent(): void
    {
        $seo = new Seo();

        $result = $seo
            ->setMetaTitle('Title')->setMetaDescription('Description')->setMetaAuthor('Author')->setMetaRobots('noindex')
            ->setExtraMetadata('<meta>')
            ->setOgType('article')->setOgTitle('OG title')->setOgDescription('OG description')->setOgUrl('https://example.com/x')
            ->setOgArticleAuthor('A')->setOgArticlePublisher('P')->setOgArticleSection('S')
            ->setTwitterTitle('T title')->setTwitterDescription('T description')->setTwitterSite('@site')->setTwitterCreator('@me')
        ;

        $this->assertSame($seo, $result);
        $this->assertSame(
            ['Title', 'Description', 'Author', 'noindex', '<meta>', 'article', 'OG title', 'OG description', 'https://example.com/x', 'A', 'P', 'S', 'T title', 'T description', '@site', '@me'],
            [
                $seo->getMetaTitle(), $seo->getMetaDescription(), $seo->getMetaAuthor(), $seo->getMetaRobots(), $seo->getExtraMetadata(),
                $seo->getOgType(), $seo->getOgTitle(), $seo->getOgDescription(), $seo->getOgUrl(),
                $seo->getOgArticleAuthor(), $seo->getOgArticlePublisher(), $seo->getOgArticleSection(),
                $seo->getTwitterTitle(), $seo->getTwitterDescription(), $seo->getTwitterSite(), $seo->getTwitterCreator(),
            ]
        );
    }

    public function testImages(): void
    {
        $seo = new Seo();
        $og = new Media();
        $twitter = new Media();

        $seo->setOgImage($og)->setTwitterImage($twitter);

        $this->assertSame($og, $seo->getOgImage());
        $this->assertSame($twitter, $seo->getTwitterImage());
        $this->assertNull($seo->setOgImage(null)->getOgImage());
    }

    public function testNewSeoIsEmpty(): void
    {
        $seo = new Seo();

        $this->assertNull($seo->getId());
        $this->assertNull($seo->getRefId());
        $this->assertNull($seo->getRefEntityName());
        $this->assertNull($seo->getMetaTitle());
        $this->assertSame(SeoType::class, $seo->getDefaultAdminType());
    }

    public function testSetRefStoresIdAndClassOfTheEntity(): void
    {
        $page = new TestPage(12);

        $seo = (new Seo())->setRef($page);

        $this->assertSame(12, $seo->getRefId());
        $this->assertSame(TestPage::class, $seo->getRefEntityName());
    }
}
