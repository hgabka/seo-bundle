<?php

namespace Hgabka\SeoBundle\Form;

use Hgabka\MediaBundle\Form\Type\MediaType;
use Hgabka\NodeBundle\Form\Type\URLChooserType;
use Hgabka\SeoBundle\Entity\Seo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SocialType. Uses the same SEO entity as the SeoType in order to prevent dataloss because there
 * is no easy way to expose a Migration from the bundles.
 */
class SocialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // OPEN GRAPH
        $builder->add('id', HiddenType::class)
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
            ->add('ogImage', MediaType::class, [
                'label' => 'hg_seo.form.og.image',
                'required' => false,
            ]);
        $builder
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
            );

        // TWITTER
        $builder->add('twitterTitle', TextType::class, [
            'label' => 'hg_seo.form.twitter.title',
            'required' => false,
            'attr' => [
                'info_text' => 'hg_seo.form.twitter.title_info_text',
            ],
        ])
            ->add('twitterDescription', TextareaType::class, [
                'label' => 'seo.form.twitter.description',
                'required' => false,
                'attr' => [
                    'info_text' => 'hg_seo.form.twitter.description_info_text',
                ],
            ])
            ->add('twitterSite', TextType::class, [
                'label' => 'seo.form.twitter.sitehandle',
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
            ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'social';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Seo::class,
        ]);
    }
}
