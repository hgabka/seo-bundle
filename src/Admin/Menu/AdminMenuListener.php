<?php

namespace Hgabka\SeoBundle\Admin\Menu;

use Doctrine\Common\Persistence\ManagerRegistry;
use Hgabka\SeoBundle\Admin\RobotsAdmin;
use Hgabka\SeoBundle\Admin\SeoAdmin;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;

class AdminMenuListener
{
    /** @var SeoAdmin */
    protected $seoAdmin;

    /** @var RobotsAdmin */
    protected $robotsAdmin;

    /**
     * AdminMenuListener constructor.
     *
     * @param MediaAdmin      $mediaAdmin
     * @param ManagerRegistry $doctrine
     * @param FolderManager   $folderManager
     */
    public function __construct(SeoAdmin $seoAdmin, RobotsAdmin $robotsAdmin)
    {
        $this->seoAdmin = $seoAdmin;
        $this->robotsAdmin = $robotsAdmin;
    }

    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        $group = $menu->getChild('hg_seo.group');

        if ($group) {
            foreach ($group->getChildren() as $key => $child) {
                $group->removeChild($key);
            }
            if ($this->seoAdmin->hasAccess('create')) {
                $url = $this->seoAdmin->generateMenuUrl('create');

                $searchCh = $group->addChild('hg_seo.admin.label.seo', [
                        'label' => 'hg_seo.admin.label.seo',
                        'route' => $url['route'],
                    ])
                                      ->setExtra('icon', '<i class="fa fa-google"></i>')
                    ;
            }
            if ($this->robotsAdmin->hasAccess('create')) {
                $url = $this->robotsAdmin->generateMenuUrl('create');

                $searchCh = $group->addChild('hg_seo.admin.label.robots', [
                        'label' => 'hg_seo.admin.label.robots',
                        'route' => $url['route'],
                    ])
                                      ->setExtra('icon', '<i class="fa fa-android"></i>')
                    ;
            }
        }
    }
}
