<?php

namespace Hgabka\SeoBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Hgabka\NodeBundle\Entity\AbstractPage;
use Hgabka\SeoBundle\Entity\Seo;
use Symfony\Component\PropertyAccess\PropertyAccess;

class SeoManager
{
    /** @var EntityManagerInterface */
    protected $manager;

    /**
     * Website title defined in your parameters.
     *
     * @var string
     */
    private $websiteTitle;

    /**
     * Saves querying the db multiple times, if you happen to use any of the defined
     * functions more than once in your templates.
     *
     * @var array
     */
    private $seoCache = [];

    /**
     * SeoManager constructor.
     */
    public function __construct(EntityManagerInterface $manager, string $websiteTitle)
    {
        $this->manager = $manager;
        $this->websiteTitle = $websiteTitle;
    }

    /**
     * @return Seo
     */
    public function getSeoFor(AbstractPage $entity)
    {
        $key = md5(\get_class($entity) . $entity->getId());

        if (!\array_key_exists($key, $this->seoCache)) {
            $seo = $this->manager->getRepository(Seo::class)->findOrCreateFor($entity);
            $general = $this->getGeneralSeo();

            if (!empty($general)) {
                $this->mergeSeo($seo, $general);
            }

            $this->seoCache[$key] = $seo;
        }

        return $this->seoCache[$key];
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
        $arr = [];

        $arr[] = $this->getSeoTitle($entity);

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
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
        $arr = [];

        $arr[] = $this->getSeoTitle();

        return $this->getPreferredValue($arr);
    }

    /**
     * @param AbstractPage $entity
     * @param null|string  $default if given we'll return this text if no SEO title was found
     *
     * @return string
     */
    public function getTitleForPageOrDefault(AbstractPage $entity = null, $default = null)
    {
        if (null === $entity) {
            return $default;
        }

        $arr = [];

        $arr[] = $this->getSeoTitle($entity);

        $arr[] = $default;

        $arr[] = $entity->getTitle();

        return $this->getPreferredValue($arr);
    }

    public function getGeneralSeo()
    {
        return $this->manager->getRepository(Seo::class)->findGeneral();
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
        $validUrl = filter_var($url, \FILTER_VALIDATE_URL);
        $host = rtrim($host, '/');

        if (false === !$validUrl) {
            // The url is valid
            return $url;
        }
        // Prepend with $host if $url starts with "/"
        if ('/' === $url[0]) {
            return $url = $host . $url;
        }

        return false;
    }

    /**
     * @param $src
     *
     * @return null|array
     */
    public function getImageDimensions($src): ?array
    {
        try {
            [$width, $height] = getimagesize($src);
        } catch (\Exception $e) {
            return null;
        }

        return ['width' => $width, 'height' => $height];
    }

    /**
     * @return string
     */
    protected function getPreferredValue(array $values)
    {
        foreach ($values as $v) {
            if (null !== $v && !empty($v)) {
                return $v;
            }
        }

        return '';
    }

    protected function mergeSeo(Seo $seo1, Seo $seo2): Seo
    {
        $properties = $this->manager->getClassMetadata(Seo::class)->getFieldNames();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($properties as $property) {
            if ('id' === $property) {
                continue;
            }

            $seo1Value = $propertyAccessor->getValue($seo1, $property);
            $seo2Value = $propertyAccessor->getValue($seo2, $property);
            if (!empty($seo1Value) || empty($seo2Value)) {
                continue;
            }

            $propertyAccessor->setValue($seo1, $property, $seo2Value);
        }

        $image1 = $seo1->getOgImage();
        $image2 = $seo2->getOgImage();

        if (empty($image1) && !empty($image2)) {
            $seo1->setOgImage($image2);
        }

        $image1 = $seo1->getTwitterImage();
        $image2 = $seo2->getTwitterImage();

        if (empty($image1) && !empty($image2)) {
            $seo1->setTwitterImage($image2);
        }

        return $seo1;
    }

    /**
     * @param AbstractPage $entity
     *
     * @return null|string
     */
    private function getSeoTitle(AbstractPage $entity = null)
    {
        $seo = $entity ? $this->getSeoFor($entity) : $this->getGeneralSeo();
        if (null !== $seo) {
            $title = $seo->getMetaTitle();
            if (!empty($title)) {
                return str_replace('%websitetitle%', $this->websiteTitle, $title);
            }
        }

        return null;
    }
}
