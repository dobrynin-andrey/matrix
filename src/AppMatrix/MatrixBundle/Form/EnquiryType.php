<?php

namespace AppMatrix\MatrixBundle\Form;

use Symfony\Component\Form\AbstractType;
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

        $builder->add('parameter_type', ChoiceType::class, array(
            'label' => 'Выберите тип параметра: ',
            'choices'  => array(
                'Ресурсы и затраты - Внешние' => 'Ресурсы и затраты - Внешние',
                'Ресурсы и затраты - Внутренние' => 'Ресурсы и затраты - Внутренние',
                'Результаты  - Внешние' => 'Результаты  - Внешние',
                'Результаты  - Внутренние' => 'Результаты  - Внутренние',
            ),
            // *this line is important*
            'choices_as_values' => true,
        ));
        $builder->add('parameter_name', TextType::class,
                array(
                    'label' => 'Введите название параметра: '
                )
            );

        $builder->add('district_type', TextType::class,
            array(
                'label' => 'Введите тип района: '
            )
        );
        $builder->add('file', FileType::class,
                array(
                    'label' => 'Загрузите файл .csv с данными параметра: ',
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }

    public function getBlockPrefix()
    {
        return 'form_add';
    }
}
