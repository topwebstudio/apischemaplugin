<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\PluginVersionRepository;

class SchemaBuilderType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title')
                ->add('content', null, array('required' => false))
                ->add('tags')
                ->add('authorEmail', null, array(
                    "mapped" => false,
                ))
                ->add('published')
                ->add('featured')
                ->add('pluginVersion', EntityType::class, array(
                    'class' => 'App:PluginVersion',
                    'query_builder' => function(PluginVersionRepository $repo) {
                        return $repo->createQueryBuilder('u')
                                ->orderBy('u.id', 'ASC');
                    },
                    'label' => 'Minimum Plugin version required',
                    'required' => false,
                    'placeholder' => 'Choose minimum plugin version',
                    //  'group_by' => 'country.name',
                    'choice_label' => 'version'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\SchemaBuilder'
        ));
    }

    public function getBlockPrefix() {
        return 'schemabuilder';
    }

}
