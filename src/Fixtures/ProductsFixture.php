<?php

namespace App\Fixtures;

use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product1 = new Products();
        $product1->setName('Iphone');
        $product1->setPriceEuro(100);
        $manager->persist($product1);
        $manager->flush();

        $product2 = new Products();
        $product2->setName('Headphone');
        $product2->setPriceEuro(20);
        $manager->persist($product2);
        $manager->flush();

        $product3 = new Products();
        $product3->setName('Case');
        $product3->setPriceEuro(10);
        $manager->persist($product3);
        $manager->flush();
    }
}
