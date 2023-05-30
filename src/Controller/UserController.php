<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class UserController extends AbstractController
{
    /**
     * Endpoint for all users
     * 
     * @Route("/api/users", name="app_api_user_getUsers", methods={"GET"})
     */
    public function getUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        
        return $this->json($users, Response::HTTP_OK, [], [
            'groups' => 'users'
        ]);
    }

    /**
     * Endpoint for a specific user
     * 
     * @Route("/api/users/{id}", name="app_api_user_getUsersById", methods={"GET"})
     */
    public function getUsersById(User $user): JsonResponse
    {

        return $this->json($user, Response::HTTP_OK, [], [
            'groups' => 'users'
        ]);
    }

    /**
     * Endpoint for all characters of a specific user
     * 
     * @Route("/api/users/{id}/characters", name="app_api_user_getCharactersByUser", methods={"GET"})
     */
    public function getCharactersByUser(User $user): JsonResponse
    {
        // Get the characters of the current user
        $charactersByUser = $user->getCharacters();
        
        return $this->json($charactersByUser, Response::HTTP_OK, [], [
            'groups' => 'charactersByUser'
        ]);
    }

    /**
     * Endpoint for all games of a specific user
     * 
     * @Route("/api/users/{id}/games", name="app_api_user_getGamesByUser", methods={"GET"})
     */
    public function getGamesByUser(User $user): JsonResponse
    {
        
        // Create a variable $gamesByUser which contains two empty arrays, player and DM
        $gamesByUser = ['player' => [], 'DM' => []];

        // Get the games of the current user as player
        $gamesUsers = $user->getGameUsers()->toArray();
        // Get the games of the current user as DM
        $gamesDM = $user->getGamesDM()->toArray();

        // For each game in which the current user is a player, an item in the player array is created
        foreach ($gamesUsers as $gameByUser) {
            $gamesByUser['player'][] = $gameByUser->getGame();
        }

        // For each game in which the current user is the DM, an item in the DM array is created
        foreach ($gamesDM as $gameDM) {
            $gamesByUser['DM'][] = $gameDM;
        }

        return $this->json($gamesByUser, Response::HTTP_OK, [], [
            'groups' => 'gamesByUser'
        ]);
    }

    /**
     * Endpoint for retrieving games without characters for a specific user
     *
     * @Route("/api/users/{id}/games/withoutCharacters", name="app_api_user_getGamesWithoutCharacters", methods={"GET"})
     */
    public function getGamesByUserWithoutCharacters(User $user): JsonResponse
    {
        // Create an empty array $gamesWithoutCharacters 
        $gamesWithoutCharacters = [];

        // Get the games of the current user as player
        $gamesUsers = $user->getGameUsers()->toArray();

        // For each game in which the current user is a player
        foreach ($gamesUsers as $gameUser) {
            // If the user character for the game doesn't exist
            if(!$user->getCharacter($gameUser->getGame())) {
                // Save the game in the $gamesWithoutCaracters array
                $gamesWithoutCharacters[] = $gameUser->getGame();
            }
        }

        return $this->json($gamesWithoutCharacters, Response::HTTP_OK, [], [
            'groups' => 'gamesByUser'
        ]);
    }


    /**
     * Endpoint for getting all invites of a specific user
     * 
     * @Route("/api/users/{id}/invites", name="app_api_user_getInvitesByUser", methods={"GET"})
     */
    public function getInvitesByUser(User $user): JsonResponse
    {

        // If the user doesn't exist, return error 404
        if (!$user) {
            return  $this->json(["error" => "Utilisateur inexistant"], Response::HTTP_NOT_FOUND);
        }

        // Get the games ID associated with the current user in the GameUsers table
        $invitesByUser = $user->getGameUsers();

        return $this->json($invitesByUser, Response::HTTP_OK, [], [
            'groups' => 'invitesByUser'
        ]);
    }

    /**
    * Get the character of a user for a specific game

    * @Route("/api/users/{userId}/games/{gameId}/character", name="app_api_user_getCharacterByUserAndGame", methods={"GET"})
    */
    public function getCharacterByUserAndGame(EntityManagerInterface $entityManager, int $userId, int $gameId): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $game = $entityManager->getRepository(Game::class)->find($gameId);

        // Get the data of the Character entity for a specific User and Game
        $gameUser = $entityManager->getRepository(Character::class)->findOneBy(['user' => $user, 'game' => $game]);

        // Check if the data exists
        if (!$gameUser) {
            return $this->json(['error' => 'Aucun personnage trouvé pour cet utilisateur et cette partie'], Response::HTTP_NOT_FOUND);
        }

        // Get the character associated with the GameUsers data
        $character = $user->getCharacter($game);

        return $this->json($character, Response::HTTP_OK, [], [
            'groups' => 'users'
        ]);
    }

    /**
     * Endpoint for adding a user
     * 
     * @Route("/api/users", name="app_api_user_postUsers", methods={"POST"})
     */
    public function postUsers(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
        ): JsonResponse
    {

        // Get the request content
        $data = $request->getContent();

        // If invalid JSON, return JSON to inform of the invalidity
        try{
            // Deserializing JSON into an Entity
            $user = $serializer->deserialize($data, User::class, "json");
            // Hashing the password
            $passwordHashed = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            $user->setPassword($passwordHashed);
        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }

        // Manually checking the integrity of the Entity
        $errors = $validator->validate($user);
        // If $errors > 0, the form is invalid
        if(count($errors) > 0){
            // Empty array to stock all errors
            $dataErrors = [];

            foreach($errors as $error){
                // Listing all errors of a specific form input into an array
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Add the user in the DB
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->json($user, Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_user_getUsersById", ["id" => $user->getId()])
        ], [
            'groups' => 'users'
        ]);
    }

    /**
     * Endpoint for editing a user
     * 
     * @Route("/api/users/{id}", name="app_api_user_editUsers", methods={"PUT", "PATCH"})
     */
    public function editUsers(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        User $user,
        ValidatorInterface $validator
        ): JsonResponse
    {

        $this->denyAccessUnlessGranted('EDIT', $user);

        // Get the request content
        $data = $request->getContent();
        // Converts request content to an array
        $dataDecoded = json_decode($data, true);
        // If the request content has a new password
        if(isset($dataDecoded['password'])) {
            // Hash the new password
            $hashedPassword = password_hash($dataDecoded['password'],PASSWORD_DEFAULT);
            // Replace the old password with the new one in $dataDecoded
            $dataDecoded['password'] = $hashedPassword;
        }
        // Converts new data to a JSON
        $newData = json_encode($dataDecoded);

        // if invalid JSON, return JSON to inform of the invalidity
        try{
            // Deserializing JSON into an Entity
            $updatedUser = $serializer->deserialize($newData, User::class, "json", [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
 
        // Manually checking the integrity of the Entity
        $errors = $validator->validate($updatedUser);
        // If $errors > 0, the form is invalid
        if(count($errors) > 0){
            // Empty array to stock all errors
            $dataErrors = [];

            foreach($errors as $error){
                // Listing all errors of a specific form input into an array
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update the updatedAt field with the current date and time
        $updatedUser->setUpdatedAt(new DateTimeImmutable());
        
        // Edit the user in the DB
        $entityManager->flush();
 
        return $this->json($user, Response::HTTP_OK, [
            "Location" => $this->generateUrl("app_api_user_getUsersById", ["id" => $updatedUser->getId()])
        ], [
            'groups' => 'users'
        ]);
    }

    /**
     * Endpoint for deleting a user
     * 
     * @Route("/api/users/{id}", name="app_api_user_deleteUsers", methods={"DELETE"})
     */
    public function deleteUsers(User $user, EntityManagerInterface $entityManager): JsonResponse
    {

        $this->denyAccessUnlessGranted('DELETE', $user);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    
}
