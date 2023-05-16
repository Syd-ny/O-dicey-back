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
     * @Route("", name="app_backoffice_game_list")
     */
    public function list(GameRepository $gameRepository): Response
    {

        $games = $gameRepository->findAll();

        return $this->render('backoffice/game/index.html.twig', [
            'games' => $games,
        ]);
    }

    /**
    * @Route("/{id}", name="app_backoffice_game_show", methods={"GET"}, requirements={"id"="\d+"})
    */
    public function show(Game $game): Response
    {
        return $this->render('backoffice/game/show.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route("/new", name="app_backoffice_game_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $game->setcreatedAt(new DateTimeImmutable(date("Y-m-d H:i:s")));

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
     * @Route("/{id}/edit", name="app_backoffice_game_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Game $game): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $game->setupdatedAt(new DateTimeImmutable(date("Y-m-d H:i:s")));

            // register game informations in the database
            $entityManager->flush();
    
            return $this->redirectToRoute('app_backoffice_game_list');
        }

        return $this->renderForm('backoffice/game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    /**
    * @Route("/{id}", name="app_backoffice_game_delete", methods={"POST"}, requirements={"id"="\d+"})
    */
    public function delete(Request $request, Game $game, GameRepository $gameRepository): Response
    {
        // ! implement the CSRF tokens validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $gameRepository->remove($game, true);
        }

        return $this->redirectToRoute('app_backoffice_game_list', [], Response::HTTP_SEE_OTHER);
    }

}
