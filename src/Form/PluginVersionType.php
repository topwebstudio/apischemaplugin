<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PluginVersionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('version', null, array('label' => 'Plugin Version'))
                ->add('archive', FileType::class, array(
                    'data_class' => null,
                    'required' => false,
                    'label' => 'Plugin Archive (ZIP file)'
                ))
                ->add('testedVersion', null, array('label' => 'Latest WP Version Supported'))
                ->add('description', null, array('required' => false))
                ->add('installation', null, array('required' => false))
                ->add('changelog', null, array('required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\PluginVersion',
        ));
    }

    public function getBlockPrefix() {
        return 'pluginversion';
    }

}
