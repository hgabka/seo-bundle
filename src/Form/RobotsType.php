<?php

namespace Hgabka\SeoBundle\Form;

use Hgabka\SeoBundle\Entity\Robots;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * SeoType.
 */
class RobotsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('robotsTxt', TextareaType::class, [
            'label' => 'robots.txt',
            'attr' => [
                'rows' => 15,
            ],
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'hgabkaseobundle_settings_form_type';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Robots::class,
        ]);
    }
}
