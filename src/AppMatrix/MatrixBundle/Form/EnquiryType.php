<?php

namespace AppMatrix\MatrixBundle\Form;

use AppMatrix\MatrixBundle\Entity\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnquiryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('parameter_type', ChoiceType::class, array(
                'label' => 'Тип параметра: ',
                'choices'  => array(
                    'Ресурсы и затраты - Внешние' => '1',
                    'Ресурсы и затраты - Внутренние' => '2',
                    'Результаты - Внешние' => '3',
                    'Результаты - Внутренние' => '4',
                ),
                'choices_as_values' => true,
            ))
            ->add('parameter_name', TextType::class,
                array(
                    'label' => 'Название параметра: '
                )
            )
            ->add('file', FileType::class,
                array(
                    'label' => 'Файл: ',
                )
            )
            ->add('project_id', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Form::class,
            'method' => 'POST',
            'attr' => [
                'class' => 'blogger'
            ]
        ]);
    }

    public function getBlockPrefix()
    {
        return 'form_add';
    }
}
