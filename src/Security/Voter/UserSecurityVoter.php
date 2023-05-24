<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserSecurityVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        
        return in_array($attribute, [self::EDIT, self::DELETE])
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
        }

        return false;
    }

}
