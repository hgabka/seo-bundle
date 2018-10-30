<?php

namespace Hgabka\SeoBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RobotsAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'robots';

    /**
     * Get the list of actions that can be accessed directly from the dashboard.
     *
     * @return array
     */
    public function getDashboardActions()
    {
        $actions = [];

        if ($this->hasAccess('create')) {
            $actions['create'] = [
                'label' => 'hg_seo.admin.label.robots',
                'translation_domain' => 'messages',
                'url' => $this->generateUrl('create'),
                'icon' => 'share-square',
            ];
        }

        return $actions;
    }

    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

        return array_intersect_key($list, ['list' => null]);
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('hg_seo.admin.label.robots')
            ->add('robotsTxt', TextareaType::class, [
                'label' => 'robots.txt',
                'attr' => [
                    'rows' => 15,
                ],
                'required' => false,
            ])
            ->end()
        ;
    }
}
