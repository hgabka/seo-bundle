<?php

namespace Hgabka\SeoBundle\Twig;

use Doctrine\ORM\EntityManager;
use Hgabka\NodeBundle\Entity\AbstractPage;
use Hgabka\SeoBundle\Entity\Seo;
use Hgabka\SeoBundle\Helper\SeoManager;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extensions for Seo.
 */
class SeoTwigExtension extends AbstractExtension
{
    /**
     * @var EntityManager
     */
    protected $em;

    /** @var SeoManager */
    protected $seoManager;

    public function __construct(EntityManager $em, SeoManager $seoManager)
    {
        $this->em = $em;
        $this->seoManager = $seoManager;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('render_seo_metadata_for', $this->renderSeoMetadataFor(...), ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('render_general_seo_metadata', $this->renderGeneralSeoMetadata(...), ['is_safe' => ['html'], 'needs_environment' => true]),
            new TwigFunction('get_seo_for', $this->getSeoFor(...)),
            new TwigFunction('get_title', $this->getTitle(...)),
            new TwigFunction('get_title_for', $this->getTitleFor(...)),
            new TwigFunction('get_title_for_page_or_default', $this->getTitleForPageOrDefault(...)),
            new TwigFunction('get_absolute_url', $this->getAbsoluteUrl(...)),
            new TwigFunction('get_image_dimensions', $this->getImageDimensions(...)),
        ];
    }

    /**
     * Validates the $url value as URL (according to » http://www.faqs.org/rfcs/rfc2396), optionally with required components.
     * It will just return the url if it's valid. If it starts with '/', the $host will be prepended.
     *
     * @param string $url
     * @param string $host
     *
     * @return string
     */
    public function getAbsoluteUrl($url, $host = null)
    {
        return $this->seoManager->getAbsoluteUrl($url, $host);
    }

    /**
     * @param object $entity      The entity
     * @param mixed  $currentNode The current node
     * @param string $template    The template
     *
     * @return string
     */
    public function renderSeoMetadataFor(Environment $environment, $entity, $currentNode = null, $template = '@HgabkaSeo/SeoTwigExtension/metadata.html.twig')
    {
        $seo = $this->seoManager->getSeoFor($entity);
        $template = $environment->load($template);

        return $template->render(
            [
                'seo' => $seo,
                'entity' => $entity,
                'currentNode' => $currentNode,
            ]
        );
    }

    /**
     * @param object $entity      The entity
     * @param mixed  $currentNode The current node
     * @param string $template    The template
     *
     * @return string
     */
    public function renderGeneralSeoMetadata(Environment $environment, $template = '@HgabkaSeo/SeoTwigExtension/metadata.html.twig')
    {
        $seo = $this->seoManager->getGeneralSeo();
        $template = $environment->load($template);

        return $template->render(
            [
                'seo' => $seo,
                'currentNode' => null,
            ]
        );
    }

    /**
     * @param $src
     *
     * @return null|array
     */
    public function getImageDimensions($src)
    {
        return $this->seoManager->getImageDimensions($src);
    }

    /**
     * The first value that is not null or empty will be returned.
     *
     * @param AbstractPage $entity the entity for which you want the page title
     *
     * @return string The page title. Will look in the SEO meta first, then the NodeTranslation, then the page.
     */
    public function getTitleFor(AbstractPage $entity)
    {
        return $this->seoManager->getTitleFor($entity);
    }

    /**
     * The first value that is not null or empty will be returned.
     *
     * @param AbstractPage $entity the entity for which you want the page title
     *
     * @return string The page title. Will look in the SEO meta first, then the NodeTranslation, then the page.
     */
    public function getTitle()
    {
        return $this->seoManager->getTitle();
    }

    /**
     * @param AbstractPage $entity
     * @param null|string  $default if given we'll return this text if no SEO title was found
     *
     * @return string
     */
    public function getTitleForPageOrDefault(?AbstractPage $entity = null, $default = null)
    {
        return $this->seoManager->getTitleForPageOrDefault($entity, $default);
    }
}
