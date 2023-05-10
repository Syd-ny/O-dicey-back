<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Repository\GameRepository;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/gallery", name="app_api_gallery_")
 */
class GalleryController extends AbstractController
{
    ///**
    // * @Route("/", name="")
    // */
    //public function index(): JsonResponse
    //{
    //    return $this->json([
    //        'message' => 'Welcome to your new controller!',
    //        'path' => 'src/Controller/GalleryController.php',
    //    ]);
    //}

    /**
    * @Route("", name="browse", methods={"GET"})
    */
    public function browse(GalleryRepository $galleryRepository): JsonResponse
    {
        $galleries = $galleryRepository->findAll();
        
        if (count($galleries) === 0) {
            return $this->json('Aucune image trouvée', Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($galleries, Response::HTTP_OK, [], ['groups' => ['gallery_list']]);
    }

    /**
    * @Route("/{id}", name="read", methods={"GET"})
    */
    public function read(GalleryRepository $gallery): JsonResponse
    {

        if ($gallery === null){return $this->json("ce joueur n'existe pas", Response::HTTP_NOT_FOUND);}

        return $this->json($gallery,200,[], ["groups"=> ["gallery_read"]]);
    }
}
