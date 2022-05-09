<?php

namespace Hgabka\SeoBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Hgabka\NodeBundle\Entity\HasNodeInterface;
use Hgabka\NodeBundle\Event\AdaptFormEvent;
use Hgabka\SeoBundle\Entity\Seo;
use Hgabka\SeoBundle\Form\SeoType;
use Hgabka\SeoBundle\Form\SocialType;
use Hgabka\UtilsBundle\Helper\FormWidgets\FormWidget;
use Hgabka\UtilsBundle\Helper\FormWidgets\Tabs\Tab;

/**
 * This will add a seo tab on each page.
 */
class NodeListener
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function adaptForm(AdaptFormEvent $event): void
    {
        if ($event->getPage() instanceof HasNodeInterface && !$event->getPage()->isStructureNode()) {
            // @var Seo $seo
            $seo = $this->em->getRepository(Seo::class)->findOrCreateFor($event->getPage());

            $seoWidget = new FormWidget();
            $seoWidget->addType('seo', SeoType::class, $seo);
            $event->getTabPane()->addTab(new Tab('hg_seo.tab.seo.title', $seoWidget));

            $socialWidget = new FormWidget();
            $socialWidget->addType('social', SocialType::class, $seo);
            $socialWidget->setTemplate('@HgabkaSeo/Admin/Social/social.html.twig');
            $event->getTabPane()->addTab(new Tab('hg_seo.tab.social.title', $socialWidget));
        }
    }
}
