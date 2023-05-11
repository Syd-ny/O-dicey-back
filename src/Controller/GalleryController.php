<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Game;
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
 * @Route("/api/galleries", name="app_api_gallery_")
 */
class GalleryController extends AbstractController
{

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
    public function read(Gallery $gallery): JsonResponse
    {

        if ($gallery === null){return $this->json("ce joueur n'existe pas", Response::HTTP_NOT_FOUND);}

        return $this->json($gallery,200,[], ["groups"=> ["gallery_read"]]);
    }

    /**
     * @Route("/games/{gameId}", name="get_galleries_by_game", requirements={"gameId"="\d+"},  methods={"GET"})
     */
    public function getGalleriesByGame(int $gameId, GalleryRepository $galleryRepository, GameRepository $gameRepository): JsonResponse
    {
        $game = $gameRepository->find($gameId);

        if (!$game) {
            return $this->json('Jeu introuvable', Response::HTTP_NOT_FOUND);
        }

        $galleries = $galleryRepository->findByGame($game);

        if (count($galleries) === 0) {
            return $this->json('Aucune image trouvée pour ce jeu', Response::HTTP_NOT_FOUND);
        }

        return $this->json($galleries, 200, [], ["groups"=> ["gallery_read"]]);
    }


    /**
    * @Route("", name="add", methods={"POST"})
    */
    public function add(
        //important to import GameRepository to show the FK Game.
        GameRepository $gameRepository,
        EntityManagerInterface $entityManager,
        Request $request, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {

        // we get the object
        $jsonContent = $request->getContent();
        if ($jsonContent === ""){
            return $this->json("Le contenu de la requête est invalide",Response::HTTP_BAD_REQUEST);
        }

        // Convert JSON into php object.
        $gallery = $serializer->deserialize( $jsonContent, Gallery::class,'json', ['datetime_format' => 'Y-m-d\TH:i:sP']);

        $errors = $validator->validate($gallery);
        if (count($errors) > 0) {
            return $this->json($errors,response::HTTP_UNPROCESSABLE_ENTITY);
        }
        //dd($gallery);

        // decode the content on JSON
        $data = json_decode($jsonContent, true);

        //take the decoded data of game
        $gameId = $data["game"]["id"] ?? null;
        $game = $gameId ? $gameRepository->find($gameId) : null;
    
        if (!$game) {
            return $this->json("Ce jeu n'existe pas", Response::HTTP_BAD_REQUEST);
        }
        $gallery->setGame($game);

        $entityManager->persist($gallery);
        $entityManager->flush();

        return $this->json($gallery,201,[], ["groups"=> ["gallery_read"]]);
    }

    /**
    * @Route("/{id}", name="edit", requirements={"id"="\d+"}, methods={"PUT","PATCH"})
    */
    public function edit(
        //important to import GameRepository to show the FK Game.
        Gallery $gallery,
        GameRepository $gameRepository,
        EntityManagerInterface $entityManager,
        Request $request, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {

        // we get the object
        $jsonContent = $request->getContent();
        if ($jsonContent === ""){
            return $this->json("Le contenu de la requête est invalide",Response::HTTP_BAD_REQUEST);
        }

        // Convert JSON into php object.
        $updatedGallery = $serializer->deserialize( $jsonContent, Gallery::class,'json', ['object_to_populate' => $gallery], ['datetime_format' => 'Y-m-d\TH:i:sP']);

        $errors = $validator->validate($updatedGallery);
        if (count($errors) > 0) {
            return $this->json($errors,response::HTTP_UNPROCESSABLE_ENTITY);
        }
        //dd($gallery);

        // decode the content on JSON
        $data = json_decode($jsonContent, true);

        //take the decoded data of game
        $gameId = $data["game"]["id"] ?? null;
        $game = $gameId ? $gameRepository->find($gameId) : null;
    
        if (!$game) {
            return $this->json("le jeu n'existe pas", Response::HTTP_BAD_REQUEST);
        }
        $updatedGallery->setGame($game);

        $entityManager->flush();

        return $this->json($updatedGallery,201,[], ["groups"=> ["gallery_read"]]);
    }

    /**
    * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
    */
    public function delete(
        $id,
        Gallery $gallery,
        GalleryRepository $galleryRepository,
        EntityManagerInterface $entityManager): JsonResponse
    {

        $gallery = $galleryRepository->find($id);

        if ($gallery === null){
            return $this->json("personnage introuvable avec cet ID :" . $id,Response::HTTP_NOT_FOUND);
        }

        $galleryRepository->remove($gallery);
        $entityManager->flush();

        return $this->json("personnage supprimé avec succès", Response::HTTP_OK);
    }
}
