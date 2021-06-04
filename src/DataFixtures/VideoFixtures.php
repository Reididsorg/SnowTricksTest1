<?php


namespace App\DataFixtures;


use App\Entity\Trick;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class VideoFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        Factory::create('fr-FR');

        $tricks = $manager->getRepository(Trick::class)->findAll();

        foreach ($tricks as $trick)
        {
            $video1 = new Video();
            $video1->setName("Video 1");
            $video1->setUrl("https://www.youtube.com/embed/tHHxTHZwFUw");
            $video1->setTrick($trick);

            $video2 = new Video();
            $video2->setName("Video 2");
            $video2->setUrl("https://www.youtube.com/embed/tHHxTHZwFUw");
            $video2->setTrick($trick);

            $manager->persist($video1);
            $manager->persist($video2);
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