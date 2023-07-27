<?php

namespace App\Form;

use App\Entity\MessageInternaute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageInternauteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,['attr'=>['class'=>'form-control form-control-lg', 'autocomplete'=>'off']])
            ->add('email', EmailType::class,['attr'=>['class'=>'form-control form-control-lg', 'autocomplete'=>"off"]])
            ->add('telephone', TelType::class,[
                'attr'=>['class'=>'form-control form-control-lg', 'autocomplete'=>"off", 'placeholder'=>"Facultatif"],
                'required' => false
            ])
            ->add('objet', TextType::class,['attr'=>['class'=>'form-control form-control-lg', 'autocomplete'=>"off"]])
            ->add('content', TextareaType::class,[
                'attr'=>['class' => 'form-control form-control-lg', 'row'=>10, 'cols'=>30]
            ])
            //->add('createdAt')
            //->add('lecture')
            //->add('retour')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MessageInternaute::class,
        ]);
    }
}
