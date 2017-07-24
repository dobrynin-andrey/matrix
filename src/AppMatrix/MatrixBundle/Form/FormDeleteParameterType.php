<?php

namespace AppMatrix\MatrixBundle\Form;

use AppMatrix\MatrixBundle\Entity\FormDeleteParameter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormDeleteParameterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('yes', SubmitType::class,
            [
                'label' => "Да"
            ]
        );

        $builder->add('no', ButtonType::class,
            [
                'label' => "Нет"
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormDeleteParameter::class,
            'method' => 'POST',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'form_delete';
    }
}
