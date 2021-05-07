<?php


namespace App\Forms;


use App\Entity\Comment;
use App\Repository\CommentRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    protected CommentRepository $commentRepository;

    public function __construct (
        CommentRepository $commentRepository
    )
    {
        $this->commentRepository = $commentRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content',
                TextareaType::class,
                [
                    'label' => 'Ton commentaire',
                    'help' => 'Contenu de ton commentaire'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}