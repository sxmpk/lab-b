<?php

namespace App\Form;

use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', TextType::class, ['label' => 'Miasto', 'empty_data' => ''])
            ->add('country', CountryType::class, ['label' => 'Państwo', 'placeholder' => 'Wybierz państwo', 'empty_data' => ''])
            ->add('latitude', NumberType::class, ['label' => 'Szerokość geograficzna', 'input' => 'string', 'scale' => 7, 'empty_data' => ''])
            ->add('longitude', NumberType::class, ['label' => 'Długość geograficzna', 'input' => 'string', 'scale' => 7, 'empty_data' => ''])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
