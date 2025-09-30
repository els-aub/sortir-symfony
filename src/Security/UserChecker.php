<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Entity\User;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getParticipant() && !$user->getParticipant()->isActif()) {
            throw new CustomUserMessageAuthenticationException('Votre compte a été désactivé. Veuillez contacter un administrateur.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // ?????? quoi ajouter
    }
}
