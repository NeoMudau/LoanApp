<?php
// src/DataFixtures/UserFixtures.php
namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('superadmin@gmail.com');
        $user->setRoles(['ROLE_SUPERADMIN']);
        $user->setIsVerified(true);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin1234');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        $user = new User();
        $user->setEmail('admin@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setIsVerified(true);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'admin1234');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}
