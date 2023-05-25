<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Mode;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Gallery;
use App\Entity\GameUsers;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;


class GameController extends AbstractController
{
    /**
    * Endpoint for all games with infos of all relations
    * 
    * @Route("/api/games", name="app_api_game_getGames", methods={"GET"})
    */
    public function getGames(EntityManagerInterface $entityManager): JsonResponse
    {

        // Get an array of all Games
        $games = $entityManager->getRepository(Game::class)->findAll();
          
        return $this->json($games,Response::HTTP_OK,[], ["groups" => "games"]);
    }

    /**
    * Endpoint for a specific game
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
     * Endpoint for all characters of a specific game
     * 
     * @Route("/api/games/{id}/characters", name="app_api_game_getCharactersByGame", methods={"GET"})
     */
    public function getCharactersByGame(Game $game): JsonResponse
    {
        // Get the characters of the current game
        $charactersByGame = $game->getCharacters();
        
        return $this->json($charactersByGame, Response::HTTP_OK, [], [
            'groups' => 'charactersByGame'
        ]);
    }
    
    /**
     * Endpoint for all users of a specific game
     * 
     * @Route("/api/games/{id}/users", name="app_api_game_getUsersByGame", methods={"GET"})
     */
    public function getUsersByGame(Game $game, EntityManagerInterface $entityManager): JsonResponse
    {
        // Get the characters of the current game
        $usersByGame =  $entityManager->getRepository(GameUsers::class)->findBy(['game' => $game]);
        
        return $this->json($usersByGame, Response::HTTP_OK, [], [
            'groups' => 'usersByGame'
        ]);
    }

    /**
    * Endpoint for all galleries of a specific game
    * 
    * @Route("/api/games/{id}/galleries", name="app_api_game_getGalleriesByGame", requirements={"gameId"="\d+"},  methods={"GET"})
    */
    public function getGalleriesByGame(Game $game): JsonResponse
    {
        // Get the galleries of the current game
        $galleriesByGame = $game->getGalleries();

        if (!$game) {
            return $this->json('Partie introuvable', Response::HTTP_NOT_FOUND);
        }

        if (count($galleriesByGame) === 0) {
            // If no pictures yet, return an empty array
            return $this->json([], Response::HTTP_NOT_FOUND);
        }

        return $this->json($galleriesByGame, 200, [], ["groups"=> ["gallery_read"]]);
    }


    /**
    * Endpoint for creating a game
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
        // Get the request content (JSON)
        $data = $request->getContent();

        // If JSON invalid, returns a JSON to specify that it is invalid
        try{
            // Deserialize JSON into an Entity
            $game = $serializer->deserialize($data,Game::class, "json");

        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"],Response::HTTP_BAD_REQUEST);
        }

        // Manually checks if the Entity is valid
        $errors = $validator->validate($game);
        // If $errors contains at least 1 item, the form is invalid
        if(count($errors) > 0){
            // Create an empty array and store all errors in it
            $dataErrors = [];

            // Loop over errors
            foreach($errors as $error){
                // Create in the array an index by fields and list all errors of the field in question in a sub-table
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            // Entity not treatable because of incorrect data, return a code 422
            return $this->json($dataErrors,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Converts request content to an array
        $dataDecoded = json_decode($data, true);
        
        // We check if the mode of the request matches an existing mode 
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

        // Link the $mode and the $dm to the $game
        $game->setMode($mode);
        $game->setDm($dm);

        // Add the DM role to the user who created the game and does not yet have this role
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
        
        //  Provide the link of the created resource
        return $this->json($game, Response::HTTP_CREATED,[
            "Location" => $this->generateUrl("app_api_game_getGames", ["id" => $game->getId()])],
            ["groups" => "newGame"]
        );
    }

    /**
    * Endpoint to invite a user to a game
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

        // Get the request content (JSON)
        $data = $request->getContent();
        $dataDecoded = json_decode($data, true);

        $userId = $dataDecoded['user'];
        
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return $this->json("Le joueur n'existe pas", Response::HTTP_NOT_FOUND);
        }

        // Check if the user selected/invited is the DM
        if ($user == $game->getDm()) {
            return $this->json("Vous êtes déjà le maître du jeu de cette partie !", Response::HTTP_BAD_REQUEST);
        }
        
        // Check if the invitation already exists
        $existingInvitation = $entityManager->getRepository(GameUsers::class)->findOneBy(['game' => $game, 'user' => $user]);
        
        if ($existingInvitation) {
             return $this->json("Cette invitation a déjà été faite", Response::HTTP_BAD_REQUEST);
        }

        // If JSON invalid, return a JSON to specify that it is invalid
        try{
            // Deserialize JSON into an entity
            $gameUser = $serializer->deserialize($data, GameUsers::class, "json");

            $user = $entityManager->getRepository(User::class)->find($dataDecoded['user']);
            
            $gameUser->setUser($user);
            $gameUser->setGame($game);
        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"],Response::HTTP_BAD_REQUEST);
        }

        // Manually checks if the Entity is valid
        $errors = $validator->validate($gameUser);
        // If $errors contains at least 1 item, the form is invalid
        if(count($errors) > 0){
            // Create an empty array and store all errors in it
            $dataErrors = [];

            // Loop over errors
            foreach($errors as $error){
                // Create in the array an index by fields and list all errors of the field in question in a sub-table
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            // Entity not being treatable because of incorrect data, return a code 422
            return $this->json($dataErrors,Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Persist the invite
        $entityManager->persist($gameUser);
        $entityManager->flush();
        
        //  Provide the link of the created resource
        return $this->json($gameUser, Response::HTTP_CREATED,[
            "Location" => $this->generateUrl("app_api_game_getGamesById", ["id" => $game->getId()])
        ], ["groups" => "gameUsers"]);
    }

    /**
    * Endpoint for editing a game
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

        // Get the request content (JSON)
        $data = $request->getContent();

        // If JSON invalid, return a JSON to specify that it is invalid
        try{
            // Deserialize JSON into an Entity
            $updatedGame = $serializer->deserialize($data,Game::class, "json", [AbstractNormalizer::OBJECT_TO_POPULATE => $game]);
        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"],Response::HTTP_BAD_REQUEST);
        }

        // Manually checks if the Entity is valid
        $errors = $validator->validate($updatedGame);
        // If $errors contains at least 1 item, the form is invalid
        if(count($errors) > 0){
            // Create an empty array and store all errors in it.
            $dataErrors = [];

            // Loop over errors
            foreach($errors as $error){
                // Create in the array an index by fields and list all errors of the field in question in a sub-table
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            // Entity not treatable because of incorrect data, return a code 422
            return $this->json($dataErrors,Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        // Converts the request content to an array
        $dataDecoded = json_decode($data, true);
        // If request contains a new mode
        if (isset($dataDecoded["mode"])) {
            // We check if the mode of the request matches an existing mode 
            $modeId = $dataDecoded["mode"] ?? null;
            $mode = $modeId ? $entityManager->getRepository(Mode::class)->find($modeId) : null;
            // If not, returns an error response
            if (!$mode) {
                return $this->json("Ce mode n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Link the $mode to the $game
            $game->setMode($mode);
        }
        // If the request contains a new DM
        if (isset($dataDecoded["dm"])) {
            // We check if the $dmId of the request matches the ID of an existing user 
            $dmId = $dataDecoded["dm"] ?? null;
            $dm = $dmId ? $entityManager->getRepository(User::class)->find($dmId) : null;
            // If not, returns an error response
            if (!$dm) {
                return $this->json("Cet utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Link the $dm to the $game
            $game->setDm($dm);
        }

        // Update the updatedAt field with the current date and time
        $game->setUpdatedAt(new DateTimeImmutable());

        // Edit the game in the DB
        $entityManager->flush();
        
        //  Provide the link of the updated resource
        return $this->json($game, Response::HTTP_OK,[
          "Location" => $this->generateUrl("app_api_game_getGamesById", ["id" => $updatedGame->getId()])
        ],["groups" => "games"]);
    }

    /**
    * Endpoint for deleting a game
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
     * Endpoint to remove a user from a game
     * 
     * @Route("/api/games/{id}/users/{user_id}", name="app_api_game_deleteGameUser", methods={"DELETE"})
     * @ParamConverter("game", options={"mapping": {"id": "id"}})
     * @ParamConverter("user", options={"mapping": {"user_id": "id"}})
     */
    public function deleteGameUser(Game $game, User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        
        // Check if the current user is the creator of the game
        $this->denyAccessUnlessGranted('DELETEINVITE', $game);

        // Check if the user is linked to the game
        $gameUser = $entityManager->getRepository(GameUsers::class)->findOneBy(['game' => $game, 'user' => $user]);
       
        if (!$gameUser) {
            return $this->json("L'utilisateur n'est pas lié à cette partie", Response::HTTP_NOT_FOUND);
        }

        // Remove the link
        $entityManager->remove($gameUser);
        $entityManager->flush();

        return $this->json("L'utilisateur a été supprimé de la partie avec succès", Response::HTTP_OK);
    }
}
