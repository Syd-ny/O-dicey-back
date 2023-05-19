<?php

namespace App\Controller\Backoffice;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserSearchType;
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
     * @Route("", name="app_backoffice_user_list")
     */
    public function list(Request $request, UserRepository $userRepository): Response
    {
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');

        $users = $userRepository->findBySearchUser($search, $sort, $order);

        return $this->render('backoffice/user/index.html.twig', [
            'users' => $users,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_user_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(User $user): Response
    {
        return $this->render('backoffice/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/new", name="app_backoffice_user_new", methods={"GET", "POST"})
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

    /**
     * @Route("/{id}/edit", name="app_backoffice_user_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {

        $form = $this->createForm(UserType::class, $user, ["custom_option" => "edit"]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setUpdatedAt(new \DateTimeImmutable());
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_backoffice_user_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_user_delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        // implementation of the CSRF token validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_backoffice_user_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/searchByMail", name="app_backoffice_user_searchByMail", methods={"GET"})
     */
    public function searchByMail(Request $request, UserRepository $userRepository): Response
    {
        $email = $request->query->get('email');

        // Effectuez votre recherche d'utilisateurs en fonction de l'e-mail ici
        $users = $userRepository->findByEmail($email);

        return $this->render('backoffice/user/index.html.twig', [
            'users' => $users,
        ]);
    }
}
