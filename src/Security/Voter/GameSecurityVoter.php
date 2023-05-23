<?php

namespace App\Security\Voter;

use App\Entity\Game;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GameSecurityVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const POSTINVITE = 'POSTINVITE';

    protected function supports(string $attribute, $subject): bool
    {
        
        return in_array($attribute, [self::EDIT, self::DELETE, self::POSTINVITE])
            && $subject instanceof \App\Entity\Game;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // If the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // If the game is inactive, do not allow editing or deleting (even from the DM)
        if ($this->isInactive($subject)) {
            return false;
        }
        
        // Check conditions and return true to grant permission
        switch ($attribute) {
            case self::EDIT:
                // If the game subject has been created by the current user connected, they can edit it
                if($user == $subject->getDm()) {
                    return true;
                }
                break;
            case self::DELETE:
                // If the game subject has been created by the current user connected, they can delete it
                if($user == $subject->getDm()) {
                    return true;
                }
                break;
            case self::POSTINVITE:
                // If the game subject has been created by the current user connected, they can invite players
                if($user == $subject->getDm()) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * Checks if the status of a game is inactive
     *
     * @param Game $subject
     * @return boolean
     */
    private function isInactive($subject): bool
    {

        if(!is_null($subject->getUpdatedAt())) {
            if($subject->getStatus() == 2) {
        
                return true;
            }
        }
        
        return false;
    }
}
