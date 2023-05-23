<?php

namespace App\Security\Voter;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GallerySecurityVoter extends Voter
{
    public const POST = 'POST';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';

    private $userRepository;

    public function __construct(UserRepository $userRepository) {

        $this->userRepository = $userRepository;
    }

    protected function supports(string $attribute, $subject): bool
    {
        
        return in_array($attribute, [self::POST, self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Gallery;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        
        $user = $token->getUser();
        $userEntity = $this->userRepository->loadUserByIdentifier($user->getUserIdentifier());
        // If the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Check conditions and return true to grant permission
        switch ($attribute) {
            case self::POST:
                // If the gallery subject is associated with a game that the user is DMing, they can post it
                if(in_array($subject->getGame(), $userEntity->getGamesDM()->toArray(), true)) {
                    return true;
                }
                break;
            case self::EDIT:
                // If the gallery subject is associated with a game that the user is DMing, they can edit it
                if(in_array($subject->getGame(), $userEntity->getGamesDM()->toArray(), true)) {
                    return true;
                }
                break;
            case self::DELETE:
                // If the gallery subject is associated with a game that the user is DMing, they can delete it
                if(in_array($subject->getGame(), $userEntity->getGamesDM()->toArray(), true)) {
                    return true;
                }
                break;
        }

        return false;
    }
}
