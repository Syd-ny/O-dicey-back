<?php

namespace App\Controller\Backoffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/logout")
 */
class LogoutController extends AbstractController
{
    /**
     * Endpoint for the disconnection to the backoffice
     * 
     * @Route("", name="app_logout")
     */
    public function logout(): void
    {
    }
}
