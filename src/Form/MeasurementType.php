<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Measurement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, ['label' => 'Data', 'widget' => 'single_text', 'input' => 'datetime', 'empty_data' => null])
            ->add('celsius', NumberType::class, ['label' => 'Temperatura (°C)', 'empty_data' => ''])
            ->add('humidity', IntegerType::class, ['label' => 'Wilgotność (%)', 'empty_data' => ''])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => static fn (Location $location): string => $location->getCity().' ('.$location->getCountry().')',
                'label' => 'Lokalizacja',
                'placeholder' => 'Wybierz lokalizację',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Measurement::class,
        ]);
    }
}
