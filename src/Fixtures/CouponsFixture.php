<?php

namespace App\Fixtures;

use App\Entity\Coupons;
use App\Enum\PaymentCouponTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $coupon1 = new Coupons();
        $coupon1->setType(PaymentCouponTypeEnum::FIXED);
        $coupon1->setCode('D15');
        $coupon1->setValue(5);
        $manager->persist($coupon1);
        $manager->flush();

        $coupon2 = new Coupons();
        $coupon2->setType(PaymentCouponTypeEnum::PERCENT);
        $coupon2->setCode('G25');
        $coupon2->setValue(6);
        $manager->persist($coupon2);
        $manager->flush();
    }
}
