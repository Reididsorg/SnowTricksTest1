<?php


namespace App\DataFixtures;


use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();
        $faker = Factory::create('fr-FR');

        for ($i = 1; $i < 11; $i++) {
            //$trickName = $faker->sentence(1) . ' : ' . $i;
            $trickName = 'Trick ' . $i;
            $trick = new Trick();
            $trick->setName($trickName);
            //$trick->setSlug($trickName);
            $trick->setDescription($faker->sentence(30) . ' ' . $i);
            $trick->setCategory($faker->randomElement($categories));
            $trick->setUser($faker->randomElement($users));
            $manager->persist($trick);
            $this->addReference('Trick '.$i, $trick);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        // TODO: Implement getDependencies() method.
        return [
            CategoryFixtures::class,
            UserFixtures::class,
        ];
    }
}