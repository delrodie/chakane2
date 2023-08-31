<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('reference')
            ->add('titre', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"]
            ])
            ->add('description', TextareaType::class,[
                'attr' => ['style'=>null],
                'required' => false
            ])
            ->add('montant', IntegerType::class,['attr'=>['class'=>'form-control']])
            ->add('solde', IntegerType::class,[
                'attr'=>['class'=>'form-control'],
                'required' => false
            ])
            ->add('taille', ChoiceType::class,[
                'attr' => ['class'=>'form-control select2'],
                'choices' => [
                    '-- Selectionnez --' => ' ',
                    'S' => 'S',
                    'M' => "M",
                    'L' => "L",
                    'XL' => "XL",
                    'XXL' => "XXL",
                ],
                'required' => false,
                'multiple' => false
            ])
            ->add('couleur', TextType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'required' => false
            ])
            ->add('poids', NumberType::class,[
                "attr"=>['class'=>'form-control', 'autocomplete'=>"off"],
                'required' => false
            ])
            ->add('media', FileType::class,[
                'attr'=>['class'=>"dropify", 'data-preview' => ".preview"],
                'label' => "Télécharger la photo d'illustration",
                'mapped' => false,
                'multiple' => false,
                'constraints' => [
                    new File([
                        'maxSize' => "20000k",
                        'mimeTypes' =>[
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                            'image/avif',
                        ],
                        //'mimeTypesMessage' => "Votre fichier doit être de type image",
                        //'maxSizeMessage' => "La taille de votre image doit être inférieure à 2Mo",
                    ])
                ],
                'required' => false
            ])
            //->add('photos')
            ->add('flag', ChoiceType::class,[
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ]
            ])
            ->add('promotion', CheckboxType::class,[
                'attr' => ['class' => 'form-check-input primary check-outline outline-primary'],
                'label_attr' => ['class' => 'custom-control-label'],
                'required' => false
            ])
            ->add('stock', IntegerType::class,[
                'attr'=>['class'=>'form-control', 'autocomplete'=>"off"],
                'required' => false
            ])
            ->add('tags', TextType::class,[
                'attr'=>['class'=>'form-control', 'data-role'=>'tagsinput'],
                'required' => true,
                'label' => "Mots clés"
            ])
            ->add('conseil', TextareaType::class,[
                'attr' => ['class' => 'form-control', 'style'=>null],
                'required' => false
            ])
            ->add('type',null,[
                'attr' => ['class' => 'form-select select2']
            ])
            ->add('categories', EntityType::class,[
                'attr'=>['class'=>'form-control select2'],
                'class' => Categorie::class,
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('c')->orderBy('c.titre', "ASC");
                },
                'choice_label' => "titre",
                'multiple' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
