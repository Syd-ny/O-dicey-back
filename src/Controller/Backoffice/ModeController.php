<?php

namespace App\Controller\Backoffice;

use App\Entity\Mode;
use App\Form\ModeType;
use App\Repository\ModeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/backoffice/modes")
 * 
 */
class ModeController extends AbstractController
{
    /**
     * @Route("", name="app_backoffice_mode_list")
     */
    public function list(ModeRepository $modeRepository): Response
    {

        $modes = $modeRepository->findAll();

        return $this->render('backoffice/mode/index.html.twig', [
            'modes' => $modes,
        ]);
    }
}
