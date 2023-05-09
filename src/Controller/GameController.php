<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\ModeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameController extends AbstractController
{
    /**
     * endpoint for all games with infos of all relations
     * 
     * @Route("/games", name="app_game_getGames", methods={"GET"})
     */
    public function getGames(GameRepository $gameRepository): JsonResponse
    {

        // get entities table of games
        $games = $gameRepository->findAll();
        
        
        return $this->json($games,Response::HTTP_OK,[], ["groups" => "games"]);
    }

     /**
     * endpoint for a specific game
     * 
     * @Route("/games/{id}", name="app_game_getGamesById", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getGamesById(Game $game): JsonResponse
    {

        // TODO gérer si le film n'existe pas

        return $this->json($game,Response::HTTP_OK,[], ["groups" => "games"]);
    }

      /**
     * endpoint for adding a game
     * 
     * @Route("/games", name="app_game_postGames", methods={"POST"})
     */
    public function postGames(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserRepository $userRepository, ModeRepository $modeRepository): JsonResponse
    {
        // Get request content (json)
        $data = $request->getContent();

        // If JSON invalid, return a json to specify that it is invalid
        try{
            // Deserialize JSON into an entity
            $game = $serializer->deserialize($data,Game::class, "json");

        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"],Response::HTTP_BAD_REQUEST);
        }

        // check if entity is valid
        $errors = $validator->validate($game);
        // If error array is upper 0, the form is invalid.
        if(count($errors) > 0){
            // Create an empty array and store all errors in it.
            $dataErrors = [];

            // Loop over errors
            foreach($errors as $error){
                // Create in my table an index by fields and list all errors of the field in question in a sub-table
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            // Entity not being treatable because of incorrect data, return a code 422
            return $this->json($dataErrors,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $dataDecoded = json_decode($data, true);
        $modeId = $dataDecoded["mode"] ?? null;
        $mode = $modeId ? $modeRepository->find($modeId) : null;

        if (!$mode) {
            return $this->json("Ce mode n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        $dmId = $dataDecoded["dm"] ?? null;
        $dm = $dmId ? $userRepository->find($dmId) : null;

        if (!$dm) {
            return $this->json("Cette utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        $game->setMode($mode);
        $game->setDm($dm);

        // Add the game in the BDD
        $entityManager->persist($game);
        $entityManager->flush();
        

        //  Provide the link of the resource created
        return $this->json(["creation successful"], Response::HTTP_CREATED,[
            "Location" => $this->generateUrl("app_game_getGamesById", ["id" => $game->getId()])
        ]);
    }

      /**
     * endpoint for editing a game
     * 
     * @Route("/games/{id}", name="app_game_editGames", methods={"PUT", "PATCH"})
     */
    public function editGames(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserRepository $userRepository, ModeRepository $modeRepository): JsonResponse
    {
        // Get request content (json)
        $data = $request->getContent();

        // If JSON invalid, return a json to specify that it is invalid
        try{
            // Deserialize JSON into an entity
            $updatedGame = $serializer->deserialize($data,Game::class, "json");

        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"],Response::HTTP_BAD_REQUEST);
        }

        // check if entity is valid
        $errors = $validator->validate($updatedGame);
        // If error array is upper 0, the form is invalid.
        if(count($errors) > 0){
            // Create an empty array and store all errors in it.
            $dataErrors = [];

            // Loop over errors
            foreach($errors as $error){
                // Create in my table an index by fields and list all errors of the field in question in a sub-table
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            // Entity not being treatable because of incorrect data, return a code 422
            return $this->json($dataErrors,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // $dataDecoded = json_decode($data, true);
        // if ($dataDecoded["mode"]) {
        //     $modeId = $dataDecoded["mode"] ?? null;
        //     $mode = $modeId ? $modeRepository->find($modeId) : null;
    
        //     if (!$mode) {
        //         return $this->json("Ce mode n'existe pas", Response::HTTP_BAD_REQUEST);
        //     }

        //     $game->setMode($mode);
        // }

        // if ($dataDecoded["dm"]) {
        //     $dmId = $dataDecoded["dm"] ?? null;
        //     $dm = $dmId ? $userRepository->find($dmId) : null;

        //     if (!$dm) {
        //         return $this->json("Ce n'existe pas", Response::HTTP_BAD_REQUEST);
        //     }

        //     $game->setDm($dm);
        // }

        // Edit the game in the BDD
        $entityManager->flush();
        
        dd($updatedGame);

        //  Provide the link of the resource created
        return $this->json(["update successful"], Response::HTTP_OK,[
            "Location" => $this->generateUrl("app_game_getGamesById", ["id" => $updatedGame->getId()])
        ]);
    }

    /**
     * @Route("/games/{id}", name="app_game_deleteGames", methods={"DELETE"})
     */
    public function deleteGames(Game $game, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($game);
        $entityManager->flush();

        return $this->json(["successful removal"], Response::HTTP_NO_CONTENT);
    }
}
