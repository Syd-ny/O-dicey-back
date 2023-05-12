<?php

namespace App\Controller\Backoffice;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/backoffice/games", name="app_backoffice_game_list")
     */
    public function list(GameRepository $gameRepository): Response
    {

        $games = $gameRepository->findAll();

        return $this->render('backoffice/game/index.html.twig', [
            'games' => $games,
        ]);
    }
}
