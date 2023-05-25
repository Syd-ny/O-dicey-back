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
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/backoffice/games")
 * 
 */
class GameController extends AbstractController
{
    /**
     * Endpoint for all games
     * 
     * @Route("", name="app_backoffice_game_getGames")
     */
    public function getGames(Request $request, GameRepository $gameRepository, PaginatorInterface $paginator): Response
    {
        // Variables to determine the display order of the games
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');

        // Use the method findBySearchGame of the game repository to search the games according to the variables
        $games = $gameRepository->findBySearchGame($search, $sort, $order);

        $pagination = $paginator->paginate(
            $games, // refers to repository
            $request->query->getInt('page', 1), // Current page number
            15 // Number of items per page
        );

        return $this->render('backoffice/game/index.html.twig', [
            'pagination' => $pagination,
            'games' => $pagination->getItems(),
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    /**
    * Endpoint for a specific game
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
     * Endpoint for creating a new game
     * 
     * @Route("/new", name="app_backoffice_game_postGames", methods={"GET", "POST"})
     */
    public function postGames(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Instance of the Game entity
        $game = new Game();

        // Instance of the GameType class using as starting data the instance of the Game $game
        $form = $this->createForm(GameType::class, $game);

        // Processing the form data
        $form->handleRequest($request);

        // If the form has been completed and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Register game information in the database
            $entityManager->persist($game);
            $entityManager->flush();
    
            $this->addFlash("success", "Partie créée !");

            return $this->redirectToRoute('app_backoffice_game_getGames', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    /**
     * Endpoint for editing a game
     * 
     * @Route("/{id}/edit", name="app_backoffice_game_editGames", methods={"GET", "POST"})
     */
    public function editGames(Request $request, EntityManagerInterface $entityManager, Game $game): Response
    {
         // Instance of the GameType class using as starting data the instance of the Game $game
        $form = $this->createForm(GameType::class, $game);
        // Processing the form data
        $form->handleRequest($request);
        // If the form has been completed and is valid
        if ($form->isSubmitted() && $form->isValid()) {

            // Update of updatedAt to the current date and time
            $game->setupdatedAt(new DateTimeImmutable());

            // Register game information in the database
            $entityManager->persist($game);
            $entityManager->flush();

            $this->addFlash("warning", "Partie modifiée !");
    
            return $this->redirectToRoute('app_backoffice_game_getGames', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    /**
    * Endpoint for deleting a game 
    *
    * @Route("/{id}", name="app_backoffice_game_deleteGames", methods={"POST"}, requirements={"id"="\d+"})
    */
    public function deleteGames(Request $request, Game $game, GameRepository $gameRepository): Response
    {
        // Implementation of the CSRF token validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $gameRepository->remove($game, true);
            $this->addFlash("danger", "Partie supprimée !");
        }

        return $this->redirectToRoute('app_backoffice_game_getGames', [], Response::HTTP_SEE_OTHER);
    }
}
