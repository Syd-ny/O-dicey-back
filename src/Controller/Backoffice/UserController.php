<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    /**
     * @Route("/backoffice/users/new", name="app_backoffice_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository,UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // retrieving the plain password
            $plainPassword = $user->getPassword();
            // hashing the paswword
            $passwordHash = $passwordHasher->hashPassword($user,$plainPassword);
            // set the password
            $user->setPassword($passwordHash);

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_backoffice_user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
