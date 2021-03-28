<?php

namespace Hgabka\SeoBundle\Helper\Menu;

use Hgabka\UtilsBundle\Helper\Menu\MenuAdaptorInterface;
use Hgabka\UtilsBundle\Helper\Menu\MenuBuilder;
use Hgabka\UtilsBundle\Helper\Menu\MenuItem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SeoManagementMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children.
     *
     * @param MenuBuilder $menu      The MenuBuilder
     * @param MenuItem[]  &$children The current children
     * @param MenuItem    $parent    The parent Menu item
     * @param Request     $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (null !== $parent && ('KunstmaanAdminBundle_settings' === $parent->getRoute()) && $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')) {
            $menuItem = new MenuItem($menu);
            $menuItem
                ->setRoute('HgabkaSeoBundle_settings_robots')
                ->setLabel('Robots')
                ->setUniqueId('robots_settings')
                ->setParent($parent);
            if (0 === stripos($request->attributes->get('_route'), $menuItem->getRoute())) {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}
