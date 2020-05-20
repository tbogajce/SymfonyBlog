<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
// this namespace by default is use Doctrine\Common\Persistence\ObjectManager;
// and it should be changed to this because it throws an error
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++) {
            $microPost = new MicroPost();
            $microPost->setText('Some random text ' . rand(0, 100));
            $microPost->setTime(new \DateTime('2020-05-20'));
            $manager->persist($microPost);
        }

        $manager->flush();
    }
}
