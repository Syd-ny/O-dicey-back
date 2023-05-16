<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GallerySecurityVoter extends Voter
{
    public const POST = 'POST';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        return in_array($attribute, [self::POST, self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Gallery;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        dd($user);
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // if the game subject has been created by the current user connected, they can edit it
                if($user->getGamesDM() == $subject->getUser()) {
                    return true;
                }
                break;
            case self::DELETE:
                // if the game subject has been created by the current user connected, they can delete it
                if($user == $subject->getUser()) {
                    return true;
                }
                break;
        }

        return false;
    }
}
