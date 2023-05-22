<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/home")
 * 
 */
class MainController extends AbstractController
{
    /**
     * Endpoint for displaying the backoffice homepage
     * 
     * @Route("", name="app_backoffice_home")
     */
    public function home(): Response
    {
        return $this->render('backoffice/main/home.html.twig');
    }

}