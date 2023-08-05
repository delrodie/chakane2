<?php

namespace App\Form;

use App\Entity\ProduitImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('media', FileType::class,[
                'attr'=>['class'=>"form-control"],
                'label' => "Télécharger la photo d'illustration",
                'mapped' => false,
                'multiple' => true,
                'required' => false
            ])
            //->add('produit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProduitImage::class,
        ]);
    }
}
