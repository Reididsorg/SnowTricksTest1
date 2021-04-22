<?php


namespace App\Forms;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',
                TextType::class,
                [
                    'label' => 'Nom d\'utilisateur'
                ]
            )

            ->add('email',
                EmailType::class,
                [
                    'label' => 'Email'
                ]

            )

//            ->add('password',
//                RepeatedType::class,
//                [
//                    'type' => PasswordType::class,
//                    'invalid_message' => 'Les 2 mots de passe doivent être identiques.',
//                    'first_options' => [
//                        'label' => 'Mot de passe'
//                    ],
//                    'second_options' => [
//                        'label' => 'Confirmer le mot de passe'
//                    ]
//                ]
//            )

            ->add('password',
                PasswordType::class,
                [
                    'label' => 'Mot de passe'
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}