<?php

namespace App\Controller\Backoffice;

use App\Entity\Game;
use App\Form\GameType;
use DateTimeImmutable;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backoffice/games")
 * 
 */
class GameController extends AbstractController
{
    /**
     * endpoint for all games
     * 
     * @Route("", name="app_backoffice_game_getGames")
     */
    public function getGames(Request $request, GameRepository $gameRepository): Response
    {
        // Variables to determine the display order of the games
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');

        // Use the method findBySearchGame of the game repository to search the games according to the variables
        $games = $gameRepository->findBySearchGame($search, $sort, $order);

        return $this->render('backoffice/game/index.html.twig', [
            'games' => $games,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    /**
    * endpoint for a specific game
    *
    * @Route("/{id}", name="app_backoffice_game_getGamesById", methods={"GET"}, requirements={"id"="\d+"})
    */
    public function getGamesById(Game $game): Response
    {
        return $this->render('backoffice/game/show.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * endpoint for create a new game
     * 
     * @Route("/new", name="app_backoffice_game_postGames", methods={"GET", "POST"})
     */
    public function postGames(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Instantiation of the Game entity
        $game = new Game();

        // Instantiation of the GameType class using as starting data the instance of the Game $game class
        $form = $this->createForm(GameType::class, $game);

        // Processing of the form entry
        $form->handleRequest($request);

        // if the form has been entered and the validation rules are checked
        if ($form->isSubmitted() && $form->isValid()) {
            // register game informations in the database
            $entityManager->persist($game);
            
            $entityManager->flush();
    
            return $this->redirectToRoute('app_backoffice_game_list');
        }

        return $this->renderForm('backoffice/game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    /**
     * endpoint for editing a game
     * 
     * @Route("/{id}/edit", name="app_backoffice_game_editGames", methods={"GET", "POST"})
     */
    public function editGames(Request $request, EntityManagerInterface $entityManager, Game $game): Response
    {
         // Instantiation of the GameType class using as starting data the instance of the Game $game class
        $form = $this->createForm(GameType::class, $game);
        // Processing of the form entry
        $form->handleRequest($request);
        // if the form has been entered and the validation rules are checked
        if ($form->isSubmitted() && $form->isValid()) {

            // Update of updatedAt to the date of the last modification
            $game->setupdatedAt(new DateTimeImmutable());

            // register game informations in the database
            $entityManager->persist($game);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_backoffice_game_list');
        }

        return $this->renderForm('backoffice/game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    /**
    * endpoint for deleting a game 
    *
    * @Route("/{id}", name="app_backoffice_game_deleteGames", methods={"POST"}, requirements={"id"="\d+"})
    */
    public function deleteGames(Request $request, Game $game, GameRepository $gameRepository): Response
    {
        // implementation of the CSRF token validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $gameRepository->remove($game, true);
        }

        return $this->redirectToRoute('app_backoffice_game_list', [], Response::HTTP_SEE_OTHER);
    }
}
