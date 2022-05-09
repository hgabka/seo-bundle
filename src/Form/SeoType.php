<?php

namespace Hgabka\SeoBundle\Form;

use Hgabka\SeoBundle\Entity\Seo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoType extends AbstractType
{
    public const ROBOTS_NOINDEX = 'noindex';
    public const ROBOTS_NOFOLLOW = 'nofollow';
    public const ROBOTS_NOARCHIVE = 'noarchive';
    public const ROBOTS_NOSNIPPET = 'nosnippet';
    public const ROBOTS_NOTRANSLATE = 'notranslate';
    public const ROBOTS_NOIMAGEINDEX = 'noimageindex';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class)
            ->add('metaTitle', null, [
                'label' => 'hg_seo.form.seo.meta_title.label',
                'attr' => [
                    'info_text' => 'hg_seo.form.seo.meta_title.info_text',
                    'maxlength' => 55,
                ],
            ])
            ->add('metaDescription', null, [
                'label' => 'hg_seo.form.seo.meta_description.label',
                'attr' => [
                    'maxlength' => 155,
                ],
            ]);

        $builder->add('metaRobots', ChoiceType::class, [
            'choices' => [
                'hg_seo.form.robots.noindex' => self::ROBOTS_NOINDEX,
                'hg_seo.form.robots.nofollow' => self::ROBOTS_NOFOLLOW,
                'hg_seo.form.robots.noarchive' => self::ROBOTS_NOARCHIVE,
                'hg_seo.form.robots.nosnippet' => self::ROBOTS_NOSNIPPET,
                'hg_seo.form.robots.notranslate' => self::ROBOTS_NOTRANSLATE,
                'hg_seo.form.robots.noimageindex' => self::ROBOTS_NOIMAGEINDEX,
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

        $builder->get('metaRobots')
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
        $builder->add('extraMetadata', TextareaType::class, [
            'label' => 'hg_seo.form.seo.extra_metadata.label',
            'required' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'seo';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => Seo::class,
        ]);
    }
}
