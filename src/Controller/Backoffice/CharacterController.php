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

    // ! Method not optimized - rework v2
    // /**
    //  * Endpoint for editing a character
    //  * 
    //  * @Route("/backoffice/characters/{id}/edit", name="app_backoffice_character_editCharacters", requirements={"id"="\d+"})
    //  */
    // public function editCharacters(Request $request, Character $character, CharacterRepository $characterRepository): Response
    // {
    //     // Instance of the CharacterType class using as starting data the instance of the Character $character
    //     $form = $this->createForm(CharacterType::class, $character, ["custom_option" => "edit"]);

    //     // Processing the form data
    //     $form->handleRequest($request);

    //     // If the form has been completed and is valid
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Update of updatedAt to the current date and time
    //         $character->setUpdatedAt(new \DateTimeImmutable());
            
    //         $characterRepository->add($character, true);

    //         return $this->redirectToRoute('app_backoffice_user_getUsersById', ['id' => $character->getUser()->getId()], Response::HTTP_SEE_OTHER);
    //     }
        
    //     return $this->renderForm('backoffice/character/edit.html.twig', [
    //         'character' => $character,
    //         'form' => $form,
    //     ]);
    // }

    /**
     * Endpoint for deleting a character
     * 
     * @Route("/backoffice/characters/{id}", name="app_backoffice_character_deleteCharacters", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function deleteCharacters(Request $request, Character $character, CharacterRepository $characterRepository): Response
    {
        // Implementation of the CSRF token validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->request->get('_token'))) {
            $characterRepository->remove($character, true);

            $this->addFlash("danger", "Personnage bien supprimé !");
        }


        return $this->redirectToRoute('app_backoffice_user_getUsersById', ['id' => $character->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
