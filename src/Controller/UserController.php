<?php

namespace App\Controller;

use App\Repository\GameRepository;
use App\Entity\User;
use App\Repository\CharacterRepository;
use App\Repository\UserRepository;
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

class UserController extends AbstractController
{
    /**
     * endpoint for all users
     * 
     * @Route("/api/users", name="app_api_user_getUsers", methods={"GET"})
     */
    public function getUsers(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        
        return $this->json($users, Response::HTTP_OK, [], [
            'groups' => 'users'
        ]);
    }

    /**
     * endpoint for a specific user
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
     * endpoint for adding a user
     * 
     * @Route("/api/users/register", name="app_api_user_postUsers", methods={"POST"})
     */
    public function postUsers(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {

        // get the request content
        $data = $request->getContent();

        // if invalid JSON, return JSON to inform of the invalidity
        try{
            // deserializing json into entity
            $user = $serializer->deserialize($data, User::class, "json");
            // hashing the password
            $passwordHashed = password_hash($user->getPassword(), PASSWORD_DEFAULT);
            $user->setPassword($passwordHashed);
        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }

        // manually checking the integrity of the entity
        $errors = $validator->validate($user);
        // if errors > 0, the form is invalid
        if(count($errors) > 0){
            // empty array to stock all errors
            $dataErrors = [];

            foreach($errors as $error){
                // listing all errors of a specific form input into an array
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // add the user in the DB
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->json(["Creation successful"], Response::HTTP_CREATED, [
            "Location" => $this->generateUrl("app_api_user_getUsersById", ["id" => $user->getId()])
        ]);
    }

    /**
     * endpoint for editing a user
     * 
     * @Route("/api/users/{id}", name="app_api_user_editUsers", methods={"PUT", "PATCH"})
     */
    public function editUsers(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, User $user, ValidatorInterface $validator): JsonResponse
    {

        $this->denyAccessUnlessGranted('EDIT', $user);

        // get the request content
        $data = $request->getContent();
        // Converts request content to an array
        $dataDecoded = json_decode($data, true);
        // If the request content has a new password. 
        if(isset($dataDecoded['password'])) {
            // Hash the new password
            $hashedPassword = password_hash($dataDecoded['password'],PASSWORD_DEFAULT);
            // Replace the old password with the new one in $dataDecoded
            $dataDecoded['password'] = $hashedPassword;
        }
        // Converts new data to a Json
        $newData = json_encode($dataDecoded);

        // if invalid JSON, return JSON to inform of the invalidity
        try{
            // deserializing json into entity
            $updatedUser = $serializer->deserialize($newData, User::class, "json", [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        }
        catch(NotEncodableValueException $e){
            return $this->json(["error" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
 
        // manually checking the integrity of the entity
        $errors = $validator->validate($updatedUser);
        // if errors > 0, the form is invalid
        if(count($errors) > 0){
            // empty array to stock all errors
            $dataErrors = [];

            foreach($errors as $error){
                // listing all errors of a specific form input into an array
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update the updatedAt field with the current date and time
        $updatedUser->setUpdatedAt(new DateTimeImmutable());
        
        // edit the user in the DB
        $entityManager->flush();
 
        return $this->json(["Update successful"], Response::HTTP_OK, [
            "Location" => $this->generateUrl("app_api_user_getUsersById", ["id" => $updatedUser->getId()])
        ]);
    }

    /**
     * endpoint for deleting a user
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

    /**
     * endpoint for all characters of a specific user
     * 
     * @Route("/api/users/{id}/characters", name="app_api_user_getCharactersByUser", methods={"GET"})
     */
    public function getCharactersByUser(User $user): JsonResponse
    {
        // get the characters of the current user
        $charactersByUser = $user->getCharacters();
        
        return $this->json($charactersByUser, Response::HTTP_OK, [], [
            'groups' => 'charactersByUser'
        ]);
    }

    /**
     * endpoint for all games of a specific user
     * 
     * @Route("/api/users/{id}/games", name="app_api_user_getGamesByUser", methods={"GET"})
     */
    public function getGamesByUser(User $user): JsonResponse
    {
        
        // Create a variable $gamesByUser which contains two empty arrays, player and DM.
        $gamesByUser = ['player' => [], 'DM' => []];

        // get the games of the current user is player
        $gamesUsers = $user->getGameUsers()->toArray();
        // get the games of the current user is DM
        $gamesDM = $user->getGamesDM()->toArray();

        // For each game in which the current user is a player, an entry in the player table is created
        foreach ($gamesUsers as $gameByUser) {
            $gamesByUser['player'][] = $gameByUser->getGame();
        }

        // For each game in which the current user is a DM, an entry in the DM table is created
        foreach ($gamesDM as $gameDM) {
            $gamesByUser['DM'][] = $gameDM;
        }

        return $this->json($gamesByUser, Response::HTTP_OK, [], [
            'groups' => 'gamesByUser'
        ]);
    }

    /**
     * endpoint for getting all invitations of a specific user
     * 
     * @Route("/api/users/{id}/invites", name="app_api_user_getInvitesByUser", methods={"GET"})
     */
    public function getInvitesByUser(User $user): JsonResponse
    {

        // if the user doesn't exist, return error 404
        if (!$user) {
            return  $this->json(["error" => "Utilisateur inexistant"], Response::HTTP_NOT_FOUND);
        }

        // get the games ID associated with the current user in the GameUsers table
        $invitesByUser = $user->getGameUsers();

        return $this->json($invitesByUser, Response::HTTP_OK, [], [
            'groups' => 'invitesByUser'
        ]);
    }

    /**
    * Get the character of an user for a specific game

    * @Route("/api/users/{userId}/games/{gameId}/character", name="app_api_user_getCharacterByUserAndGame", methods={"GET"})
    */
    public function getCharacterByUserAndGame(UserRepository $userRepository, CharacterRepository $characterRepository, GameRepository $gameRepository, int $userId, int $gameId): JsonResponse
    {
        $user = $userRepository->find($userId);
        $game = $gameRepository->find($gameId);

        // get the entry of table Character for the user and specific game
        $gameUser = $characterRepository->findOneBy(['user' => $user, 'game' => $game]);

        // check if entry exist
        if (!$gameUser) {
            return $this->json(['error' => 'Aucun personnage trouvé pour cet utilisateur et cette partie'], Response::HTTP_NOT_FOUND);
        }

        // Get the character associated with GameUsers entry
        $character = $user->getCharacter($game);

        return $this->json($character, Response::HTTP_OK, [], [
            'groups' => 'users'
        ]);
    }

    
}
