<?php


namespace App\DataFixtures;


use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 11; $i++) {
            $categoryName = 'Catégorie n° ' . $i;
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);
            $this->addReference('Category '.$i, $category);
        }
        $manager->flush();
    }
}