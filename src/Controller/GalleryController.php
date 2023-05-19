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
    *  endpoint for all galleries 
    * 
    * @Route("", name="getGalleries", methods={"GET"})
    */
    public function getGalleries(GalleryRepository $galleryRepository): JsonResponse
    {
        $galleries = $galleryRepository->findAll();
        
        if (count($galleries) === 0) {
            return $this->json('Aucune image trouvée', Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($galleries, Response::HTTP_OK, [], ['groups' => ['gallery_list']]);
    }

    /**
    * endpoint for a specific gallery
    * 
    * @Route("/{id}", name="getGalleriesById", methods={"GET"})
    */
    public function getGalleriesById(Gallery $gallery): JsonResponse
    {

        if ($gallery === null){return $this->json("Cette image n'existe pas", Response::HTTP_NOT_FOUND);}

        return $this->json($gallery,200,[], ["groups"=> ["gallery_read"]]);
    }

    /**
    * endpoint to create a gallery
    * 
    * @Route("", name="postGalleries", methods={"POST"})
    */
    public function postGalleries(
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
        
        // decode the content on JSON
        $data = json_decode($jsonContent, true);
        
        // retrieve gameId if $data["game_id"] is set
        $gameId = $data["game"] ?? null;
        $game = $gameId ? $gameRepository->find($gameId) : null;
        
        if (!$game) {
            return $this->json("Cette partie n'existe pas", Response::HTTP_BAD_REQUEST);
        }
        $gallery->setGame($game);

        // can't post a new gallery if you're not the DM of the game associated with the gallery
        $this->denyAccessUnlessGranted('POST', $gallery);
        
        $entityManager->persist($gallery);
        $entityManager->flush();

        return $this->json(["creation successful"], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_gallery_getGalleriesById", ["id" => $gallery->getId()])
        ]);
    }

    /**
    *  endpoint to edit a gallery
    * 
    * @Route("/{id}", name="editGalleries", requirements={"id"="\d+"}, methods={"PUT","PATCH"})
    */
    public function editGalleries(
        //important to import GameRepository to show the FK Game.
        Gallery $gallery,
        GameRepository $gameRepository,
        EntityManagerInterface $entityManager,
        Request $request, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {

        $this->denyAccessUnlessGranted('EDIT', $gallery);

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

        // decode the content on JSON
        $data = json_decode($jsonContent, true);

        // If request content a new game.
        if (isset($data["game"])) {
            // We check if the $gameId of the request matches the ID of an existing game 
            $gameId = $data["game"] ?? null;
            $game = $gameId ? $gameRepository->find($gameId) : null;
            // If not, returns an error response
            if (!$game) {
                return $this->json("La partie n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Add $game in $updatedGallery
            $gallery->setGame($game);
        } 

        // Edit the gallery in the DB  
        $entityManager->flush();
 
        //  Provide the link of the resource updated
        return $this->json(["update successful"], Response::HTTP_OK,[
            "Location" => $this->generateUrl("app_api_game_getGamesById", ["id" => $updatedGallery->getId()])
        ]);
    }

    /**
    * endpoint to delete a gallery
    * 
    * @Route("/{id}", name="deleteGalleries", requirements={"id"="\d+"}, methods={"DELETE"})
    */
    public function deleteGalleries(
        $id,
        Gallery $gallery,
        GalleryRepository $galleryRepository,
        EntityManagerInterface $entityManager): JsonResponse
    {

        $this->denyAccessUnlessGranted('DELETE', $gallery);

        if ($gallery === null){
            return $this->json("Image introuvable avec cet ID :" . $id,Response::HTTP_NOT_FOUND);
        }

        $galleryRepository->remove($gallery);
        $entityManager->flush();

        return $this->json("Image supprimée avec succès", Response::HTTP_OK);
    }
}
