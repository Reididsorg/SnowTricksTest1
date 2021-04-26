<?php


namespace App\Forms;

use App\Entity\Category;
use App\Entity\Trick;
use App\Repository\CategoryRepositiry;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TrickType extends AbstractType
{
    private $categoryRepository;

    public function __construct(CategoryRepositiry $categoryRepositiry)
    {
        $this->categoryRepository = $categoryRepositiry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',
                TextType::class,
                [
                    'label' => 'Nom',
                    'help' => 'Saisir un titre de 100 caractères maximum'
                ]
            )
            ->add('description',
                TextareaType::class,
                [
                    'label' => 'Description'
                ]
            )
            /*->add('slug',
                TextType::class,
                [
                    'label' => 'Slug'
                ]
            )*/
            ->add('category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'label' => 'Catégorie',
                    'choice_label' => 'name',
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('e')
                            ->orderBy('e.id', 'ASC');
                    },
                    'placeholder' => 'Choisir une catégorie'
                ]
            )
//            ->add(
//                'mainImage',
//                ImageType::class,
//                [
//                    'label' => 'Image principale'
//                ]
//            )
//            ->add(
//                'images',
//                CollectionType::class, [
//                    'entry_type' => ImageType::class,
//                    'label' => false,
//                    'allow_add' => true,
//                    'allow_delete' => true,
//                    'delete_empty' =>true
//                ]
//            )
            ->add('videos',
                CollectionType::class, [
                    'entry_type' => VideoType::class,
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' =>true
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'error_mapping' => [
                '.' => 'mainImage',
            ],
            'data_class' => Trick::class,
        ]);
    }
}