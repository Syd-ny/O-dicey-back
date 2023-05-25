<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CharacterSecurityVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Character;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // If the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Check conditions and return true to grant permission
        switch ($attribute) {
            case self::EDIT:
                // If the character subject has been created by the current user connected,
                // or if the character subject belongs to a game DMed by the current user, they can edit it
                if($user == $subject->getUser() || $user == $subject->getGame()->getDm()) {
                    return true;
                }
                break;
            case self::DELETE:
                // If the character subject has been created by the current user connected,
                // or if the character subject belongs to a game DMed by the current user, they can delete it
                if($user == $subject->getUser() || $user == $subject->getGame()->getDm()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
