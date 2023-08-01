<?php

namespace App\DataFixtures;

use App\Entity\ServiceHttpLog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $serviceHttpLog = new ServiceHttpLog();
        $serviceHttpLog->setName('USER-SERVICE');
        $serviceHttpLog->setStatusCode(200);
        $serviceHttpLog->setDate(new \DateTimeImmutable());
        $serviceHttpLog->setCreatedAt(new \DateTimeImmutable());
        $hash = md5($serviceHttpLog->getName() . $serviceHttpLog->getDate()->format("Y-m-d H:i:s") . $serviceHttpLog->getStatusCode());
        $serviceHttpLog->setHash($hash);

        $manager->persist($serviceHttpLog);

        $manager->flush();
    }
}
