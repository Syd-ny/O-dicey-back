<?php

namespace App\Controller;


use App\Entity\Character;
use App\Repository\CharacterRepository;
use App\Repository\User;
use App\Repository\Game;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/characters", name="app_api_character_")
 */
class CharacterController extends AbstractController
{

    /**
    * endpoint for all characters
    * 
    * @Route("", name="getCharacters", methods={"GET"})
    */
    public function getCharacters(CharacterRepository $characterRepository): JsonResponse
    {
        $characters = $characterRepository->findAll();

        if ($characters === null){return $this->json("Aucun joueur trouvé", Response::HTTP_NOT_FOUND);}

        return $this->json($characters,200,[], ["groups"=> ["character_list"]]);
    }

    /**
    * endpoint for a specific character
    * 
    * @Route("/{id}", name="getCharactersById", methods={"GET"})
    */
    public function getCharactersById(Character $character): JsonResponse
    {

        if ($character === null){return $this->json("ce joueur n'existe pas", Response::HTTP_NOT_FOUND);}

        return $this->json($character,200,[], ["groups"=> ["character_read"]]);
    }

    /**
    *  endpoint for adding a character
    * 
    * @Route("", name="postCharacters", methods={"POST"})
    */
    public function postCharacters(
        //important to import UserRepository and GameRepository to show the 2 FK User and Game.
        UserRepository $userRepository,
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
        $character = $serializer->deserialize( $jsonContent, Character::class,'json', ['datetime_format' => 'Y-m-d\TH:i:sP']);

        $errors = $validator->validate($character);
        if (count($errors) > 0) {
            return $this->json($errors,response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // decode the content on JSON
        $data = json_decode($jsonContent, true);
        //take the decoded data of user
        $userId = $data["user"] ?? null;
        $user = $userId ? $userRepository->find($userId) : null;

        if (!$user) {
            return $this->json("Cet utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        //take the decoded data of game
        $gameId = $data["game"] ?? null;
        $game = $gameId ? $gameRepository->find($gameId) : null;
    
        if (!$game) {
            return $this->json("Ce jeu n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        $character->setUser($user);
        $character->setGame($game);

        $entityManager->persist($character);
        $entityManager->flush();

        return $this->json($character,200,[], ["groups"=> ["character_add"]]);
    }

    /**
    *  endpoint for editing a character
    * 
    * @Route("/{id}", name="editCharacters", requirements={"id"="\d+"}, methods={"PUT","PATCH"})
    */
    public function editCharacters(
        $id,
        UserRepository $userRepository,
        GameRepository $gameRepository,
        CharacterRepository $characterRepository,
        EntityManagerInterface $entityManager,
        Request $request, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {

        
        $character = $characterRepository->find($id);

        $this->denyAccessUnlessGranted('EDIT', $character);

        if (!$character) {
            return $this->json("Le personnage n'existe pas.", Response::HTTP_NOT_FOUND);
        }
        
        // we get the Json
        $jsonContent = $request->getContent();
        if ($jsonContent === ""){
            return $this->json("Le contenu de la requête est invalide",Response::HTTP_BAD_REQUEST);
        }

        // Convert JSON into php object.
        $character = $serializer->deserialize( $jsonContent, Character::class,'json',['object_to_populate' => $character] , ['datetime_format' => 'Y-m-d\TH:i:sP']);

        $errors = $validator->validate($character);
        if (count($errors) > 0) {
            return $this->json($errors,response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Converts request content to an array
        $data = json_decode($jsonContent, true);
        // If request contains a new user.
        if (isset($data["user"])) {
            // We check if the user of the request matches to an existing user 
            $userId = $data["user"] ?? null;
            $user = $userId ? $userRepository->find($userId) : null;
            // If not, returns an error response
            if (!$user) {
                return $this->json("Cet utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Add $user in $character
            $character->setUser($user);
        }

        // If request contains a new game.
        if (isset($data["game"])) {
            // We check if the game of the request matches to an existing game 
            $gameId = $data["game"] ?? null;
            $game = $userId ? $gameRepository->find($gameId) : null;
            // If not, returns an error response
            if (!$game) {
                return $this->json("Ce jeu n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Add $game in $character
            $character->setGame($game);
        }

        // Update the updatedAt field with the current date and time
        $character->setUpdatedAt(new DateTimeImmutable());

        // Edit the character in the DB
        $entityManager->flush();

        return $this->json($character,200,[], ["groups"=> ["character_edit"]]);
    }

    /**
    * endpoint for deleting a character
    *
    * @Route("/{id}", name="deleteCharacters", requirements={"id"="\d+"}, methods={"DELETE"})
    */
    public function deleteCharacters(
        $id,
        CharacterRepository $characterRepository,
        EntityManagerInterface $entityManager): JsonResponse
    {

        $character = $characterRepository->find($id);

        $this->denyAccessUnlessGranted('DELETE', $character);

        if ($character === null){
            return $this->json("Personnage introuvable avec cet ID :" . $id,Response::HTTP_NOT_FOUND);
        }

        $characterRepository->remove($character);
        $entityManager->flush();

        return $this->json("Personnage supprimé avec succès", Response::HTTP_OK);
    }
}

