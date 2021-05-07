<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    protected EncoderFactoryInterface $encoderFactory;

    public function __construct (
        EncoderFactoryInterface $encoderFactory
    )
    {
        $this->encoderFactory = $encoderFactory;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');



            $user1 = new User();
            $user1->setUserName('bruno');
            $encoder1 = $this->encoderFactory->getEncoder(User::class);
            $passwordCrypted1 = $encoder1->encodePassword('12345', '');
            $user1->setPassword($passwordCrypted1);
            $user1->setEmail('bruno@bruno.fr');
            $manager->persist($user1);

            $user2 = new User();
            $user2->setUserName('samo');
            $encoder2 = $this->encoderFactory->getEncoder(User::class);
            $passwordCrypted2 = $encoder2->encodePassword('12345', '');
            $user2->setPassword($passwordCrypted2);
            $user2->setEmail('samo@samo.fr');
            $manager->persist($user2);

        $manager->flush();
    }

    public function getDependencies()
    {
        // TODO: Implement getDependencies() method.
        return [
            TrickFixtures::class,
        ];
    }
}