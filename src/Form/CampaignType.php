<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('campaignName')
                ->add('campaignId')
                ->add('secretKey')
        ;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Campaign',
        ));
    }

    public function getBlockPrefix() {
        return 'campaign';
    }

}
