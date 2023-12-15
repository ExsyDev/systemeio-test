<?php

namespace App\Fixtures;

use App\Entity\Taxes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaxesFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tax1 = new Taxes();
        $tax1->setCountry('germany');
        $tax1->setPercent(19);
        $tax1->setTaxNumber('DE123456789');
        $manager->persist($tax1);
        $manager->flush();

        $tax2 = new Taxes();
        $tax2->setCountry('italy');
        $tax2->setPercent(22);
        $tax2->setTaxNumber('IT12345678911');
        $manager->persist($tax2);
        $manager->flush();

        $tax3 = new Taxes();
        $tax3->setCountry('greece');
        $tax3->setPercent(24);
        $tax3->setTaxNumber('GR123456789');
        $manager->persist($tax3);
        $manager->flush();
    }
}
