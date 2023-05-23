<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Entity\Game;
use App\Entity\GameUsers;
use App\Entity\Mode;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameController extends AbstractController
{
    /**
    * endpoint for all games with infos of all relations
    * 
    * @Route("/api/games", name="app_api_game_getGames", methods={"GET"})
    */
    public function getGames(EntityManagerInterface $entityManager): JsonResponse
    {

        // get entities table of games
        $games = $entityManager->getRepository(Game::class)->findAll();
          
        return $this->json($games,Response::HTTP_OK,[], ["groups" => "games"]);
    }

    /**
    * endpoint for a specific game
    * 
    * @Route("/api/games/{id}", name="app_api_game_getGamesById", methods={"GET"}, requirements={"id"="\d+"})
    */
    public function getGamesById(Game $game): JsonResponse
    {
        
        if (!$game) {
            return $this->json("Cette partie n'existe pas", Response::HTTP_BAD_REQUEST);
        }
        
        return $this->json($game,Response::HTTP_OK,[], ["groups" => "games"]);
    }

    /**
    * endpoint to create a game
    * 
    * @Route("/api/games", name="app_api_game_postGames", methods={"POST"})
    */
    public function postGames(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
        ): JsonResponse
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

        // manually check if entity is valid
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
        
        // Converts request content to an array
        $dataDecoded = json_decode($data, true);
        
        // We check if the mode of the request matches to an existing mode 
        $modeId = $dataDecoded["mode"] ?? null;
        $mode = $modeId ? $entityManager->getRepository(Mode::class)->find($modeId) : null;
        // If not, returns an error response
        if (!$mode) {
            return $this->json("Ce mode n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        // We check if the $dmId of the request matches the ID of an existing user 
        $dmId = $dataDecoded["dm"] ?? null;
        $dm = $dmId ? $entityManager->getRepository(User::class)->find($dmId) : null;
        // If not, returns an error response
        if (!$dm) {
            return $this->json("Cet utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        // Add $mode and $dm in $game
        $game->setMode($mode);
        $game->setDm($dm);

        // Adds the DM role to the user who created the game and does not yet have this role
        $dm = $game->getDm();
        $dmRoles = $dm->getRoles();
        if(!in_array("ROLE_DM", $dmRoles)) {
            $dmRoles[] = "ROLE_DM";
            $game = $game->setDm($dm->setRoles($dmRoles));
        }
        
        // We link the gallery data of the game creation form (if there is any)
        if (isset($dataDecoded['galleries'])) {

            $gallery = new Gallery();
            $gallery->setPicture($dataDecoded['galleries'][0]);
            $gallery->setMainPicture($dataDecoded['galleries'][1]);
            $gallery->setGame($game);
            $entityManager->persist($gallery);
        }

        // Add the game in the BDD
        $entityManager->persist($game);
        
        $entityManager->flush();
        
        //  Provide the link of the resource created
        return $this->json(["Creation successful"], Response::HTTP_CREATED,[
            "Location" => $this->generateUrl("app_api_game_getGamesById", ["id" => $game->getId()])
        ]);
    }

    /**
    * endpoint to edit a game
    * 
    * @Route("/api/games/{id}", name="app_api_game_editGames", methods={"PUT", "PATCH"})
    */
    public function editGames(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        Game $game,
        ValidatorInterface $validator
        ): JsonResponse
    {

        $this->denyAccessUnlessGranted('EDIT', $game);

        // Get request content (json)
        $data = $request->getContent();

        // If JSON invalid, return a json to specify that it is invalid
        try{
            // Deserialize JSON into an entity
            $updatedGame = $serializer->deserialize($data,Game::class, "json", [AbstractNormalizer::OBJECT_TO_POPULATE => $game]);
        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"],Response::HTTP_BAD_REQUEST);
        }

        // Manually check if entity is valid
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
        
        // Converts request content to an array
        $dataDecoded = json_decode($data, true);
        // If request content a new mode.
        if (isset($dataDecoded["mode"])) {
            // We check if the mode of the request matches to an existing mode 
            $modeId = $dataDecoded["mode"] ?? null;
            $mode = $modeId ? $entityManager->getRepository(Mode::class)->find($modeId) : null;
            // If not, returns an error response
            if (!$mode) {
                return $this->json("Ce mode n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Add $mode in $game
            $game->setMode($mode);
        }
        // If request content a new dm.
        if (isset($dataDecoded["dm"])) {
            // We check if the $dmId of the request matches the ID of an existing user 
            $dmId = $dataDecoded["dm"] ?? null;
            $dm = $dmId ? $entityManager->getRepository(User::class)->find($dmId) : null;
            // If not, returns an error response
            if (!$dm) {
                return $this->json("Cet utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Add $dm in $game
            $game->setDm($dm);
        }

        // Update the updatedAt field with the current date and time
        $game->setUpdatedAt(new DateTimeImmutable());

        // Edit the game in the DB
        $entityManager->flush();
        
        //  Provide the link of the resource updated
        return $this->json(["Update successful"], Response::HTTP_OK,[
            "Location" => $this->generateUrl("app_api_game_getGamesById", ["id" => $updatedGame->getId()])
        ]);
    }

    /**
    * endpoint to delete a game
    * 
    * @Route("/api/games/{id}", name="app_api_game_deleteGames", methods={"DELETE"})
    */
    public function deleteGames(Game $game, EntityManagerInterface $entityManager): JsonResponse
    {

        $this->denyAccessUnlessGranted('DELETE', $game);

        // Delete the game
        $entityManager->remove($game);
        // Update in the DB
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * endpoint for all characters of a specific game
     * 
     * @Route("/api/games/{id}/characters", name="app_api_game_getCharactersByGame", methods={"GET"})
     */
    public function getCharactersByGame(Game $game): JsonResponse
    {
        // get the characters of the current game
        $charactersByGame = $game->getCharacters();
        
        return $this->json($charactersByGame, Response::HTTP_OK, [], [
            'groups' => 'charactersByGame'
        ]);
    }

    /**
    * endpoints for all galeries of a specific game
    * 
    * @Route("/api/games/{id}/galleries", name="app_api_game_getGalleriesByGame", requirements={"gameId"="\d+"},  methods={"GET"})
    */
    public function getGalleriesByGame(Game $game): JsonResponse
    {
        // get the characters of the current game
        $galleriesByGame = $game->getGalleries();

        if (!$game) {
            return $this->json('Partie introuvable', Response::HTTP_NOT_FOUND);
        }

        if (count($galleriesByGame) === 0) {
            return $this->json('Aucune image trouvée pour ce jeu', Response::HTTP_NOT_FOUND);
        }

        return $this->json($galleriesByGame, 200, [], ["groups"=> ["gallery_read"]]);
    }

    /**
    * Endpoint to invite an user to a game
    * 
    * @Route("/api/games/{id}/users", name="app_api_game_postGameUsersInvites", methods={"POST"})
    */
    public function postGameUsersInvites(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        Game $game,
        ValidatorInterface $validator
        ): JsonResponse
    {
        
        $this->denyAccessUnlessGranted('POSTINVITE', $game);

        // Get request content (json)
        $data = $request->getContent();
        $dataDecoded = json_decode($data, true);
        $user = $entityManager->getRepository(User::class)->find($dataDecoded['user']);

        $gameUsers = $entityManager->getRepository(GameUsers::class)->findAll();

        foreach ($gameUsers as $gameUser) {
            if($gameUser->getGame() === $game && $gameUser->getUser() === $user) {
                return $this->json("Cette invitation a déjà été faite", Response::HTTP_BAD_REQUEST);
            }
        }

        // If JSON invalid, return a json to specify that it is invalid
        try{
            // Deserialize JSON into an entity
            $gameUser = $serializer->deserialize($data,GameUsers::class, "json");
            $dataDecoded = json_decode($data, true);
            $user = $entityManager->getRepository(User::class)->find($dataDecoded['user']);
            
            $gameUser->setUser($user);
            $gameUser->setGame($game);
        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"],Response::HTTP_BAD_REQUEST);
        }

        // manually check if entity is valid
        $errors = $validator->validate($gameUser);
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

        // Add the game in the BDD
        $entityManager->persist($gameUser);
        $entityManager->flush();
        
        //  Provide the link of the resource created
        return $this->json(["Invitation created"], Response::HTTP_CREATED,[
            "Location" => $this->generateUrl("app_api_game_getGamesById", ["id" => $game->getId()])
        ]);
    }

}
