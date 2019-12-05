<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\CampaignRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('productName')
                ->add('productId')
                ->add('campaign', EntityType::class, array(
                    'class' => 'App:Campaign',
                    'query_builder' => function(CampaignRepository $repo) {
                        return $repo->createQueryBuilder('c')
                                ->orderBy('c.id', 'DESC');
                    },
                    'label' => 'Campaign',
                    'required' => true,
                    'placeholder' => 'Choose Campaign',
                    'choice_label' => 'nicename'))
                ->add('licensedWebsites')

        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Product',
        ));
    }

    public function getBlockPrefix() {
        return 'product';
    }

}
