<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/backoffice/main", name="app_backoffice_main")
     */
    public function index(): Response
    {
        return $this->render('backoffice/main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
