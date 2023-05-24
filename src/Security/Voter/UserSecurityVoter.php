<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserSecurityVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const DELETE_GAME_USER_LINK = 'REMOVE_GAME_USER_LINK';

    protected function supports(string $attribute, $subject): bool
    {
        
        return in_array($attribute, [self::EDIT, self::DELETE, self::DELETE_GAME_USER_LINK])
            && $subject instanceof \App\Entity\User;
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
                // If the user subject has the same email/identifier as the current connected user, they can edit the info
                if($subject->getEmail() === $user->getUserIdentifier()) {
                    return true;
                }
            break;
            case self::DELETE:
                // If the user subject has the same email/identifier as the current connected user, they can delete the account
                if($subject->getEmail() === $user->getUserIdentifier()) {
                    return true;
                }
            break;
            case self::DELETE_GAME_USER_LINK:
                // Check if the user has the role "ROLE_MJ" or "ROLE_ADMIN"
                if (in_array('ROLE_MJ', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
        }

        return false;
    }

}
