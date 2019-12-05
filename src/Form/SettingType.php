<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SettingType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        switch ($options['data']->getType()) {
            case 'html':
                $builder
                        ->add('value');
                break;

            case 'string':
                $builder
                        ->add('value', TextType::class, array(
                            'required' => false,
                            'label' => 'Value',
                            'attr' => array('class' => 'span7')
                ));
                break;

            case 'raw':
                $builder
                        ->add('value', TextareaType::class, array(
                            'required' => false,
                            'label' => 'Value',
                            'attr' => array('rows' => '10', 'cols' => 50, 'class' => 'span8')
                ));
                break;

            case 'integer':
                $builder
                        ->add('value', IntegerType::class, array(
                            'required' => false,
                            'label' => 'Value',
                            'attr' => array('class' => '')
                ));
                break;

            case 'boolean':
                $builder
                        ->add('value', CheckboxType::class, array(
                            'required' => false,
                            'label' => 'Value',
                            'attr' => array('class' => 'iphone')
                ));
                break;

            default:
                $builder
                        ->add('value', TextType::class, array(
                            'required' => false,
                            'label' => 'Value'
                ));
                break;
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Setting',
        ));
    }

    public function getBlockPrefix() {
        return 'setting';
    }

}
