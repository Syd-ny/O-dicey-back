<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Game;
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
    *  Endpoint for all galleries 
    * 
    * @Route("", name="getGalleries", methods={"GET"})
    */
    public function getGalleries(EntityManagerInterface $entityManager): JsonResponse
    {
        $galleries = $entityManager->getRepository(Gallery::class)->findAll();
        
        if (count($galleries) === 0) {
            // If no pictures yet, return an empty array
            return $this->json([], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($galleries, Response::HTTP_OK, [], ['groups' => ['gallery_list']]);
    }

    /**
    * Endpoint for a specific gallery
    * 
    * @Route("/{id}", name="getGalleriesById", methods={"GET"})
    */
    public function getGalleriesById(Gallery $gallery): JsonResponse
    {

        if ($gallery === null) {
            return $this->json("Cette image n'existe pas", Response::HTTP_NOT_FOUND);
        }

        return $this->json($gallery,200,[], ["groups"=> ["gallery_read"]]);
    }

    /**
    * Endpoint for creating a gallery
    * 
    * @Route("", name="postGalleries", methods={"POST"})
    */
    public function postGalleries(
        EntityManagerInterface $entityManager,
        Request $request, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {
        
        // We get the object
        $jsonContent = $request->getContent();
        if ($jsonContent === ""){
            return $this->json("Le contenu de la requête est invalide",Response::HTTP_BAD_REQUEST);
        }
        
        // Convert JSON into php object
        $gallery = $serializer->deserialize( $jsonContent, Gallery::class,'json', ['datetime_format' => 'Y-m-d\TH:i:sP']);
        
        $errors = $validator->validate($gallery);
        if (count($errors) > 0) {
            return $this->json($errors,response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Decode the content on JSON
        $data = json_decode($jsonContent, true);
        
        // Retrieve gameId if the key is set
        $gameId = $data["game"] ?? null;
        $game = $gameId ? $entityManager->getRepository(Game::class)->find($gameId) : null;
        
        if (!$game) {
            return $this->json("Cette partie n'existe pas", Response::HTTP_BAD_REQUEST);
        }
        $gallery->setGame($game);

        // Can't post a new gallery if you're not the DM of the game associated with the gallery
        $this->denyAccessUnlessGranted('POST', $gallery);
        
        $entityManager->persist($gallery);
        $entityManager->flush();

        return $this->json($gallery, Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_gallery_getGalleriesById", ["id" => $gallery->getId()])
        ], ["groups"=> ["gallery_read"]]);
    }

    /**
    *  Endpoint for editing a gallery
    * 
    * @Route("/{id}", name="editGalleries", requirements={"id"="\d+"}, methods={"PUT","PATCH"})
    */
    public function editGalleries(
        Gallery $gallery,
        EntityManagerInterface $entityManager,
        Request $request, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {

        $this->denyAccessUnlessGranted('EDIT', $gallery);

        // We get the object
        $jsonContent = $request->getContent();
        if ($jsonContent === ""){
            return $this->json("Le contenu de la requête est invalide",Response::HTTP_BAD_REQUEST);
        }

        // Convert JSON into php object
        $updatedGallery = $serializer->deserialize( $jsonContent, Gallery::class,'json', ['object_to_populate' => $gallery], ['datetime_format' => 'Y-m-d\TH:i:sP']);

        $errors = $validator->validate($updatedGallery);
        if (count($errors) > 0) {
            return $this->json($errors,response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Decode the content of JSON
        $data = json_decode($jsonContent, true);
        
        // If the request contains a new game
        if (isset($data["game_id"])) {
            // We check if the $gameId of the request matches the ID of an existing game 
            $gameId = $data["game_id"] ?? null;
            $game = $gameId ? $entityManager->getRepository(Game::class)->find($gameId) : null;
            // If not, returns an error response
            if (!$game) {
                return $this->json("La partie n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Link the $game to the $updatedGallery
            $gallery->setGame($game);
        } 

        // Edit the gallery in the DB  
        $entityManager->flush();
 
        //  Provide the link of the updated resource
        return $this->json($gallery, Response::HTTP_OK,[
            "Location" => $this->generateUrl("app_api_game_getGamesById", ["id" => $updatedGallery->getId()])
        ], ["groups"=> ["gallery_read"]]);
    }

    /**
    * Endpoint for deleting a gallery
    * 
    * @Route("/{id}", name="deleteGalleries", requirements={"id"="\d+"}, methods={"DELETE"})
    */
    public function deleteGalleries($id, Gallery $gallery, EntityManagerInterface $entityManager): JsonResponse
    {

        $this->denyAccessUnlessGranted('DELETE', $gallery);

        if ($gallery === null){
            return $this->json("Image introuvable avec cet ID :" . $id,Response::HTTP_NOT_FOUND);
        }

        $entityManager->getRepository(Gallery::class)->remove($gallery);
        $entityManager->flush();

        return $this->json("Image supprimée avec succès", Response::HTTP_OK);
    }
}
