<?php
namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'attr' => array(
                    'placeholder' => 'Enter article title'
                )
            ])
            ->add('shortDescription', null, [
//                'label' => 'Short ',
                'attr' => array(
                    'placeholder' => 'Enter short description'
                )
            ])
            ->add('content', null, [
                'label' => 'News text ',
                'attr' => array(
                    'placeholder' => 'Enter news text'
                )
            ])
            ->add('categories', null, [
                'label' => 'Category',
                //'placeholder' => 'Choose product',
            ])
            ->add('insertDate', DateTimeType::class , [
//                'label' => 'News text ',
                'widget' => 'single_text'
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the image
                // everytime you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                                "image/png",
                                "image/jpeg",
                                "image/jpg",
                                "image/gif",
                        ],
                        'mimeTypesMessage' => 'Please upload an image',
                    ])
                ],

                'attr' => array(
                    'accept' => '.png,.jpeg,.jpg,.png'
                )
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Article'
        ]);
    }


}