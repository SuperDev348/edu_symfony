<?php

namespace App\Form;

use App\Entity\Businessowner;
use Symfony\Component\Form\AbstractType;

class BusinessownerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numregistre')
            ->add('adresse')
            ->add('type')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Businessowner::class,
        ]);
    }
}
