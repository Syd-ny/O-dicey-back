<?php

namespace App\Controller\Backoffice;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/backoffice/users", name="app_backoffice_user_list")
     */
    public function list(UserRepository $userRepository): Response
    {

        $users = $userRepository->findAll();

        return $this->render('backoffice/user/index.html.twig', [
            'users' => $users,
        ]);
    }
}
