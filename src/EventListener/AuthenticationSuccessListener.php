<?php
namespace App\EventListener;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;


class AuthenticationSuccessListener
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        // When authentication is successful, retrieve the user
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        // Get the $userEntity from the UserInterface $user
        $userIdentifier = $user->getUserIdentifier();
        $userEntity = $this->userRepository->loadUserByIdentifier($userIdentifier);

        $data['data'] = array(
            'id' => $userEntity-> getId(),
            'email' => $userEntity->getUserIdentifier(),
            'login' => $userEntity->getLogin(),
            'roles' => $userEntity->getRoles()
        );

        // Save the user's info
        $event->setData($data);
    }
}