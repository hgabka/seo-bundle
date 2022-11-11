<?php

namespace Hgabka\SeoBundle\Admin;

use Hgabka\MediaBundle\Form\Type\MediaType;
use Hgabka\NodeBundle\Form\Type\URLChooserType;
use Hgabka\SeoBundle\Form\SeoType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SeoAdmin extends AbstractAdmin
{
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'seo';
    }

    /**
     * Get the list of actions that can be accessed directly from the dashboard.
     *
     * @return array
     */
    protected function configureDashboardActions(array $actions): array
    {
        $actions = [];

        if ($this->hasAccess('create')) {
            $actions['create'] = [
                'label' => 'hg_seo.admin.label.seo',
                'translation_domain' => 'messages',
                'url' => $this->generateUrl('create'),
                'icon' => 'fa fa-google',
            ];
        }

        return $actions;
    }

    protected function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $list = parent::configureActionButtons($buttonList, $action, $object);

        return array_intersect_key($list, ['list' => null]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->tab('hg_seo.tab.seo.title')
                ->with('hg_seo.block.seo')
                    ->add('metaTitle', TextType::class, [
                        'label' => 'hg_seo.form.og.title',
                        'required' => false,
                    ])
                    ->add('metaDescription', TextareaType::class, [
                        'label' => 'hg_seo.form.og.meta_description',
                        'required' => false,
                    ])
            ->add('metaRobots', ChoiceType::class, [
                'choices' => [
                    'hg_seo.form.robots.noindex' => SeoType::ROBOTS_NOINDEX,
                    'hg_seo.form.robots.nofollow' => SeoType::ROBOTS_NOFOLLOW,
                    'hg_seo.form.robots.noarchive' => SeoType::ROBOTS_NOARCHIVE,
                    'hg_seo.form.robots.nosnippet' => SeoType::ROBOTS_NOSNIPPET,
                    'hg_seo.form.robots.notranslate' => SeoType::ROBOTS_NOTRANSLATE,
                    'hg_seo.form.robots.noimageindex' => SeoType::ROBOTS_NOIMAGEINDEX,
                ],
                'required' => false,
                'multiple' => true,
                'expanded' => false,
                'label' => 'hg_seo.form.seo.meta_robots.label',
                'attr' => [
                    'placeholder' => 'hg_seo.form.seo.meta_robots.placeholder',
                    'class' => 'form-control',
                    'maxlength' => 255,
                ],
            ]);

        $form->get('metaRobots')
                ->addModelTransformer(new CallbackTransformer(
                    function ($original) {
                        // string to array
                        $array = explode(',', $original);
                        // trim all the values
                        $array = array_map('trim', $array);

                        return $array;
                    },
                    function ($submitted) {
                        // trim all the values
                        $value = array_map('trim', $submitted);
                        // join together
                        $string = implode(',', $value);

                        return $string;
                    }
                ));
        $form->add('extraMetadata', TextareaType::class, [
            'label' => 'hg_seo.form.seo.extra_metadata.label',
            'required' => false,
        ])
                ->end()
            ->end()
            ->tab('hg_seo.tab.social.title')
                ->with('hg_seo.pagetabs.opengraph')
            ->add('ogTitle', TextType::class, [
                'label' => 'hg_seo.form.og.title',
                'required' => false,
                'attr' => [
                    'info_text' => "Open Graph (OG) is a standard way of representing online objects. It's used, as example, by Facebook or other social media to build share links.",
                ],
            ])
            ->add('ogDescription', TextareaType::class, [
                'label' => 'hg_seo.form.og.description',
                'required' => false,
            ])
            ->add('ogUrl', URLChooserType::class, [
                'label' => 'hg_seo.form.og.url',
                'required' => false,
                'link_types' => [
                    URLChooserType::INTERNAL,
                    URLChooserType::EXTERNAL,
                ],
            ])
            ->add('ogType', ChoiceType::class, [
                'label' => 'hg_seo.form.og.type',
                'required' => false,
                'choices' => [
                    'Website' => 'website',
                    'Article' => 'article',
                    'Profile' => 'profile',
                    'Book' => 'book',
                    'Video' => 'video.other',
                    'Music' => 'music.song',
                ],
            ])
            ->add(
                'ogArticleAuthor',
                TextType::class,
                [
                    'label' => 'hg_seo.form.og.article.author',
                    'required' => false,
                ]
            )
            ->add(
                'ogArticlePublisher',
                TextType::class,
                [
                    'label' => 'hg_seo.form.og.article.publisher',
                    'required' => false,
                ]
            )
            ->add(
                'ogArticleSection',
                TextType::class,
                [
                    'label' => 'hg_seo.form.og.article.section',
                    'required' => false,
                ]
            )
            ->add('ogImage', MediaType::class, [
                'label' => 'hg_seo.form.og.image',
                'required' => false,
            ])

        ->end()
        ->with('hg_seo.pagetabs.twittercards')
            ->add('twitterTitle', TextType::class, [
            'label' => 'hg_seo.form.twitter.title',
            'required' => false,
            'attr' => [
                'info_text' => 'hg_seo.form.twitter.title_info_text',
            ],
        ])
                ->add('twitterDescription', TextareaType::class, [
                    'label' => 'hg_seo.form.twitter.description',
                    'required' => false,
                    'attr' => [
                        'info_text' => 'hg_seo.form.twitter.description_info_text',
                    ],
                ])
                ->add('twitterSite', TextType::class, [
                    'label' => 'hg_seo.form.twitter.sitehandle',
                    'required' => false,
                    'attr' => [
                        'info_text' => 'hg_seo.form.twitter.sitehandle_info_text',
                    ],
                ])
                ->add('twitterCreator', TextType::class, [
                    'label' => 'hg_seo.form.twitter.creatorhandle',
                    'required' => false,
                    'attr' => [
                        'info_text' => 'Twitter handle of your page publisher.',
                    ],
                ])
                ->add('twitterImage', MediaType::class, [
                    'label' => 'hg_seo.form.twitter.image',
                    'required' => false,
                ])
            ->end()
            ->end()

        ;
    }
}
