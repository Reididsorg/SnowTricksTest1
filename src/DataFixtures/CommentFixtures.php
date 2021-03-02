<?php


namespace App\DataFixtures;


use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $tricks = $manager->getRepository(Trick::class)->findAll();
        $faker = Factory::create('fr-FR');

        foreach ($tricks as $trick)
        {
            for ($i = 0; $i < 10; $i++) {
                $comment = new Comment();
                $comment->setContent($faker->sentence(50));
                //$comment->setUser();
                $comment->setTrick($trick);
                $manager->persist($comment);
            }
        }
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