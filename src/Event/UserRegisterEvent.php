<?php


namespace App\Event;


use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserRegisterEvent extends EventDispatcher
{
    // unique string to identify this particular event
    const NAME = 'user.register';

    /**
     * @var User
     */
    private $registeredUser;

    /**
     * UserRegisterEvent constructor.
     * @param User $registeredUser
     */
    public function __construct(User $registeredUser)
    {
        $this->registeredUser = $registeredUser;
    }

    /**
     * @return User
     */
    public function getRegisteredUser(): User
    {
        return $this->registeredUser;
    }





}