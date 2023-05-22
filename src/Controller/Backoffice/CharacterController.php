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
     * endpoint for editing a character
     * 
     * @Route("/backoffice/characters/{id}/edit", name="app_backoffice_character_editCharacters", requirements={"id"="\d+"})
     */
    public function editCharacters(Request $request, Character $character, CharacterRepository $characterRepository): Response
    {
        // Instantiation of the CharacterType class using as starting data the instance of the Character $character class
        $form = $this->createForm(CharacterType::class, $character, ["custom_option" => "edit"]);

        // Processing of the form entry
        $form->handleRequest($request);

        // if the form has been entered and the validation rules are checked
        if ($form->isSubmitted() && $form->isValid()) {
            // Update of updatedAt to the date of the last modification
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
     * endpoint for deleting a character
     * 
     * @Route("/backoffice/characters/{id}", name="app_backoffice_character_deleteCharacters", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function deleteCharacters(Request $request, Character $character, CharacterRepository $characterRepository): Response
    {
        // implementation of the CSRF token validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->request->get('_token'))) {
            $characterRepository->remove($character, true);
        }

        return $this->redirectToRoute('app_backoffice_user_show', ['id' => $character->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
