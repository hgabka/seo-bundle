<?php

namespace Hgabka\SeoBundle\Admin\Menu;

use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SeoBundle\Admin\SeoAdmin;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;

class AdminMenuListener
{
    /** @var SeoAdmin */
    protected $seoAdmin;

    /**
     * AdminMenuListener constructor.
     *
     * @param MediaAdmin      $mediaAdmin
     * @param ManagerRegistry $doctrine
     * @param FolderManager   $folderManager
     */
    public function __construct(SeoAdmin $seoAdmin)
    {
        $this->seoAdmin = $seoAdmin;
    }

    public function addMenuItems(ConfigureMenuEvent $event)
    {
        if ($this->seoAdmin->hasAccess('create')) {
            $menu = $event->getMenu();
            $group = $menu->getChild('hg_seo.group');

            if ($group) {
                foreach ($group->getChildren() as $key => $child) {
                    $group->removeChild($key);
                }

                $url = $this->seoAdmin->generateMenuUrl('create');

                $searchCh = $group->addChild('hg_seo.group', [
                    'label' => 'hg_seo.admin.label.robots',
                    'route' => $url['route'],
                ])
                                  ->setExtra('icon', '<i class="fa fa-share-square"></i>')
                ;
            }
        }
    }
}
