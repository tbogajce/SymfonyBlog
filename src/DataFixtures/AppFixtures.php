<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
// this namespace by default is use Doctrine\Common\Persistence\ObjectManager;
// and it should be changed to this because it throws an error
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    // CONSOLE COMMAND: php bin/console doctrine:fixtures:load

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    // needed for bcrypt encoding
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // order is important because of references
        $this->loadUsers($manager);
        $this->loadMicroPosts($manager);
    }

    private function loadMicroPosts(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++) {
            $microPost = new MicroPost();
            $microPost->setText('Some random text ' . rand(0, 100));
            $microPost->setTime(new \DateTime('2020-05-20'));
            // add reference from user for relations
            $microPost->setUser($this->getReference('tihi'));
            $manager->persist($microPost);
        }

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('tihi');
        $user->setFullName('Tihomir Bogajcevic');
        $user->setEmail('tihi@gmail.com');
        // first parameter for encodePassword is UserInterface
        // that is why we needed to implement it in User entity
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'tihisifra'));

        // added for relations
        $this->addReference('tihi', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
