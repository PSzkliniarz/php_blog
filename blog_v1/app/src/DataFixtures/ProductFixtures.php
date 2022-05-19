<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create();

         $product = new Product();
         $product->setName($this->faker->sentence);
         $product->setPrice($this->faker->numberBetween(5, 100));
         $product->setNo($this->faker->numberBetween(5, 100));
         $manager->persist($product);

        $manager->flush();
    }
}
