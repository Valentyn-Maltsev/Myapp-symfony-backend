<?php

namespace App\Form\Handler;

use App\Entity\User;
use App\Utils\Manager\UserManager;
use Symfony\Component\Form\Form;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFormHandler
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(UserManager $userManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userManager = $userManager;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    /**
     * @param User $user
     * @return User|null
     */
    public function processEditForm(Form $form)
    {
        $plainPassword = $form->get('plainPassword')->getData();
        $newEmail = $form->get('newEmail')->getData();

        /** @var User $user */
        $user = $form->getData();

        if (!$user->id) {
            $user->setEmail($newEmail);
        }

        if ($plainPassword ) {
            $encodedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($encodedPassword);
        }

        $this->userManager->save($user);

        return $user;
    }
}