<?php

namespace App\Controller\Backoffice;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CharacterController extends AbstractController
{
    /**
     * @Route("/backoffice/characters/{id}/edit", name="app_backoffice_character_edit", requirements={"id"="\d+"})
     */
    public function edit(Request $request, Character $character, CharacterRepository $characterRepository): Response
    {
        $form = $this->createForm(CharacterType::class, $character, ["custom_option" => "edit"]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $character->setUpdatedAt(new \DateTimeImmutable());
            $characterRepository->add($character, true);

            return $this->redirectToRoute('app_backoffice_user_show', ['id' => $character->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }
        
        return $this->renderForm('backoffice/character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/backoffice/characters/{id}", name="app_backoffice_character_delete", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Character $character, CharacterRepository $characterRepository): Response
    {
        // ! implement the CSRF tokens validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->request->get('_token'))) {
            $characterRepository->remove($character, true);
        }

        return $this->redirectToRoute('app_backoffice_user_show', ['id' => $character->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
