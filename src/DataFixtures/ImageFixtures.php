<?php


namespace App\DataFixtures;


use App\Entity\Image;
use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $tricks = $manager->getRepository(Trick::class)->findAll();

        foreach ($tricks as $trick)
        {
            $mainImage = new Image();
            $mainImage->setName("Backflip");
            $mainImage->setAlt("Image principale");
            $mainImage->setPath("/img/tricks/main/trick-main.png");
            $mainImage->setTrick($trick);
            $mainImage->setMain(true);

            $image1 = new Image();
            $image1->setName("Image 1");
            $image1->setAlt("Image 1");
            $image1->setPath("/img/tricks/thumbnails/trick_thumbnail.jpg");
            $image1->setTrick($trick);

            $image2 = new Image();
            $image2->setName("Image 2");
            $image2->setAlt("Image 2");
            $image2->setPath("/img/tricks/thumbnails/trick_thumbnail.jpg");
            $image2->setTrick($trick);

            $image3 = new Image();
            $image3->setName("Image 3");
            $image3->setAlt("Image 3");
            $image3->setPath("/img/tricks/thumbnails/trick_thumbnail.jpg");
            $image3->setTrick($trick);

            $manager->persist($mainImage);
            $manager->persist($image1);
            $manager->persist($image2);
            $manager->persist($image3);
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