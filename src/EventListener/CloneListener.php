<?php

namespace Hgabka\SeoBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Hgabka\SeoBundle\Entity\Seo;
use Hgabka\UtilsBundle\Event\DeepCloneAndSaveEvent;
use Hgabka\UtilsBundle\Helper\CloneHelper;

/**
 * This event will make sure the seo metadata is copied when a page is cloned.
 */
class CloneListener
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var CloneHelper
     */
    private $cloneHelper;

    /**
     * @param EntityManager $em          The entity manager
     * @param CloneHelper   $cloneHelper The clone helper
     */
    public function __construct(EntityManager $em, CloneHelper $cloneHelper)
    {
        $this->em = $em;
        $this->cloneHelper = $cloneHelper;
    }

    public function postDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $originalEntity = $event->getEntity();

        if (method_exists($originalEntity, 'getId')) {
            // @var Seo $seo
            $seo = $this->em->getRepository(Seo::class)->findFor($originalEntity);

            if (null !== $seo) {
                // @var Seo $clonedSeo
                $clonedSeo = $this->cloneHelper->deepCloneAndSave($seo);
                $clonedSeo->setRef($event->getClonedEntity());

                $this->em->persist($clonedSeo);
                $this->em->flush($clonedSeo);
            }
        }
    }
}
