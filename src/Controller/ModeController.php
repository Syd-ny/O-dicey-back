<?php

namespace App\Controller;

use App\Entity\Mode;
use App\Repository\ModeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModeController extends AbstractController
{
    /**
    * Endpoint for all modes
    * 
    * @Route("/api/modes", name="app_api_mode_getModes", methods={"GET"})
    */
    public function getModes(ModeRepository $modeRepository): JsonResponse
    {

        // Get all modes
        $modes = $modeRepository->findAll();
        
        return $this->json($modes,Response::HTTP_OK,[], ["groups" => "modes"]);
    }

    /**
    * Endpoint for a specific mode
    * 
    * @Route("/api/modes/{id}", name="app_api_mode_getModesById", methods={"GET"}, requirements={"id"="\d+"})
    */
    public function getModesById(Mode $mode): JsonResponse
    {
        return $this->json($mode,Response::HTTP_OK,[], ["groups" => "modes"]);
    }
}
