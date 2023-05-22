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


/**
 * @Route("/backoffice/users")
 * 
 */
class UserController extends AbstractController
{
    /**
     * Endpoint for all users
     * 
     * @Route("", name="app_backoffice_user_getUsers")
     */
    public function getUsers(Request $request, UserRepository $userRepository): Response
    {
        // Variables to determine the display order of the users
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');

        // Use the method findBySearchUser of the user repository to search the users according to the variables
        $users = $userRepository->findBySearchUser($search, $sort, $order);

        return $this->render('backoffice/user/index.html.twig', [
            'users' => $users,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    /**
     * Endpoint for a specific user
     * 
     * @Route("/{id}", name="app_backoffice_user_getUsersById", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getUsersById(User $user): Response
    {
        return $this->render('backoffice/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Endpoint for finding an user with his Email
     * 
     * @Route("/searchByMail", name="app_backoffice_user_getUsersByMail", methods={"GET"})
     */
    public function getUsersByMail(Request $request, UserRepository $userRepository): Response
    {
        // Get the email value from the request
        $email = $request->query->get('email');

        // Search users by email with the function findByEmail of the userRepository
        $users = $userRepository->findByEmail($email);

        return $this->render('backoffice/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Endpoint for creating an user
     * 
     * @Route("/new", name="app_backoffice_user_postUsers", methods={"GET", "POST"})
     */
    public function postUsers(Request $request, UserRepository $userRepository,UserPasswordHasherInterface $passwordHasher): Response
    {
        // Instantiation of the User entity
        $user = new User();
        // Instantiation of the UserType class using as starting data the instance of the User $user class
        $form = $this->createForm(UserType::class, $user);
        // Processing of the form entry
        $form->handleRequest($request);
        // if the form has been entered and the validation rules are checked
        if ($form->isSubmitted() && $form->isValid()) {

            // retrieving the plain password
            $plainPassword = $user->getPassword();
            // hashing the paswword
            $passwordHash = $passwordHasher->hashPassword($user,$plainPassword);
            // set the password
            $user->setPassword($passwordHash);

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_backoffice_user_getUsers', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Endpoint for editing an user
     * 
     * @Route("/{id}/edit", name="app_backoffice_user_editUsers", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function editUsers(Request $request, User $user, UserRepository $userRepository): Response
    {
        // Instantiation of the UserType class using as starting data the instance of the User $user class
        $form = $this->createForm(UserType::class, $user, ["custom_option" => "edit"]);
        // Processing of the form entry
        $form->handleRequest($request);
        // if the form has been entered and the validation rules are checked
        if ($form->isSubmitted() && $form->isValid()) {
            // Update of updatedAt to the date of the last modification
            $user->setUpdatedAt(new \DateTimeImmutable());

            $userRepository->add($user, true);

            return $this->redirectToRoute('app_backoffice_user_getUsers', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Endpoint for deleting an user
     * 
     * @Route("/{id}", name="app_backoffice_user_deleteUsers", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function deleteUsers(Request $request, User $user, UserRepository $userRepository): Response
    {
        // implementation of the CSRF token validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_backoffice_user_getUsers', [], Response::HTTP_SEE_OTHER);
    }
}
