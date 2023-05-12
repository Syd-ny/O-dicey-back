<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/backoffice/home", name="app_backoffice_home")
     */
    public function home(): Response
    {
        return $this->render('backoffice/main/home.html.twig');
    }

}
