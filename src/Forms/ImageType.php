<?php


namespace App\Forms;


use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    private $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',
                TextType::class,
                [
                    'label' => 'Nom'
                ]
            )
            ->add('alt',
                TextType::class,
                [
                    'label' => 'Description'
                ]
            )
            //->add('path',
            ->add('path',
                FileType::class,
                [
                    'label' => 'Sélectionnez un fichier'/*,
                    'mapped' => false*/
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'label' => false,
        ]);
    }
}