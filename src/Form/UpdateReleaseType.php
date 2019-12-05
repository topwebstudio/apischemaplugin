<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\RegionRepository;

class UpdateReleaseType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $id = $options['country'];

        $builder->add('version')->add('notes', null, array( 'required' => false))
                ->add('region', EntityType::class, array(
                    'class' => 'App:Region',
                    'query_builder' => function(RegionRepository $repo)  use ($id) {
                        return $repo->findAllForm($id);
                    },
                     'group_by' => 'country.name',
                    'choice_label' => 'name')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\UpdateRelease',
            'country' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'updaterelease';
    }

}
