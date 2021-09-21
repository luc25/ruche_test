<?php

namespace App\DataFixtures;

use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $superAdmin = new User();
        $superAdmin->setEmail('bigbosssantaclaus@santa.com');
        $superAdmin->setUsername('bigbosssantaclaus@santa.com');
        $superAdmin->setPassword($this->passwordHasher->hashPassword($superAdmin, 'big_boss_santa'));

        $manager->persist($superAdmin);

        $lutin = new User();
        $lutin->setEmail('lutin@santa.com');
        $lutin->setUsername('lutin@santa.com');
        $lutin->setPassword($this->passwordHasher->hashPassword($lutin, 'lutin'));

        $manager->persist($lutin);

        $lutain = new User();
        $lutain->setEmail('lutain@santa.com');
        $lutain->setUsername('lutain@santa.com');
        $lutain->setPassword($this->passwordHasher->hashPassword($lutain, 'lutain'));

        $manager->persist($lutain);

        $lut1 = new User();
        $lut1->setEmail('lut1@santa.com');
        $lut1->setUsername('lut1@santa.com');
        $lut1->setPassword($this->passwordHasher->hashPassword($lut1, 'lut1'));

        $manager->persist($lut1);

        $manager->flush();
    }
}
