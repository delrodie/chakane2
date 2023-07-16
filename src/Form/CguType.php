<?php

namespace App\Form;

use App\Entity\Cgu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CguType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class,['attr'=>['class'=>'form-control', 'autocomplete'=>"off"]])
            ->add('contenu', TextareaType::class,[
                'attr' => ['class' => 'contenu', 'style'=>null],
                'required' => false
            ])
            ->add('statut', CheckboxType::class,[
                'attr'=>['class'=>'form-check-input'],
                'required' => false
            ])
            //->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cgu::class,
        ]);
    }
}
