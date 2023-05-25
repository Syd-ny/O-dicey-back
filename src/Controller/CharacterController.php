<?php

namespace App\Controller;


use App\Entity\Character;
use App\Entity\Game;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/characters", name="app_api_character_")
 */
class CharacterController extends AbstractController
{

    /**
    * Endpoint for all characters
    * 
    * @Route("", name="getCharacters", methods={"GET"})
    */
    public function getCharacters(EntityManagerInterface $entityManager): JsonResponse
    {
        $characters = $entityManager->getRepository(Character::class)->findAll();

        if ($characters === null){return $this->json("Aucun joueur trouvé", Response::HTTP_NOT_FOUND);}

        return $this->json($characters,200,[], ["groups"=> ["character_list"]]);
    }

    /**
    * Endpoint for a specific character
    * 
    * @Route("/{id}", name="getCharactersById", methods={"GET"})
    */
    public function getCharactersById(Character $character): JsonResponse
    {

        if ($character === null){return $this->json("ce joueur n'existe pas", Response::HTTP_NOT_FOUND);}

        return $this->json($character,200,[], ["groups"=> ["character_read"]]);
    }

    /**
    *  Endpoint for adding a character
    * 
    * @Route("", name="postCharacters", methods={"POST"})
    */
    public function postCharacters(
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
        $character = $serializer->deserialize( $jsonContent, Character::class,'json', ['datetime_format' => 'Y-m-d\TH:i:sP']);

        $errors = $validator->validate($character);
        if (count($errors) > 0) {
            return $this->json($errors,response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Decode the content on JSON
        $data = json_decode($jsonContent, true);
        // Take the decoded data of user
        $userId = $data["user"] ?? null;
        $user = $userId ? $entityManager->getRepository(User::class)->find($userId) : null;

        if (!$user) {
            return $this->json("Cet utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        // Take the decoded data of game
        $gameId = $data["game"] ?? null;
        $game = $gameId ? $entityManager->getRepository(Game::class)->find($gameId) : null;
    
        if (!$game) {
            return $this->json("Ce jeu n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        // Check if the character already exists
        $existingCharacter = $entityManager->getRepository(Character::class)->findOneBy(['game' => $game, 'user' => $user]);
        
        if ($existingCharacter) {
             return $this->json("Vous avez déjà créé un personnage pour cette partie", Response::HTTP_BAD_REQUEST);
        }

        $character->setUser($user);
        $character->setGame($game);

        $entityManager->persist($character);
        $entityManager->flush();

        return $this->json($character,200,[], ["groups"=> ["character_add"]]);
    }

    /**
    *  Endpoint for editing a character
    * 
    * @Route("/{id}", name="editCharacters", requirements={"id"="\d+"}, methods={"PUT","PATCH"})
    */
    public function editCharacters(
        $id,
        EntityManagerInterface $entityManager,
        Request $request, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {
        
        $character = $entityManager->getRepository(Character::class)->find($id);

        $this->denyAccessUnlessGranted('EDIT', $character);

        if (!$character) {
            return $this->json("Le personnage n'existe pas.", Response::HTTP_NOT_FOUND);
        }
        
        // We get the JSON
        $jsonContent = $request->getContent();
        if ($jsonContent === ""){
            return $this->json("Le contenu de la requête est invalide",Response::HTTP_BAD_REQUEST);
        }

        // Convert JSON into php object
        $character = $serializer->deserialize( $jsonContent, Character::class,'json',['object_to_populate' => $character] , ['datetime_format' => 'Y-m-d\TH:i:sP']);

        $errors = $validator->validate($character);
        if (count($errors) > 0) {
            return $this->json($errors,response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Converts request content to an array
        $data = json_decode($jsonContent, true);
        // If the request contains a new user
        if (isset($data["user"])) {
            // We check if the user of the request matches an existing user 
            $userId = $data["user"] ?? null;
            $user = $userId ? $entityManager->getRepository(User::class)->find($userId) : null;
            // If not, returns an error response
            if (!$user) {
                return $this->json("Cet utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Link the $user to the $character
            $character->setUser($user);
        }

        // If the request contains a new game
        if (isset($data["game"])) {
            // We check if the game of the request matches an existing game 
            $gameId = $data["game"] ?? null;
            $game = $userId ? $entityManager->getRepository(Game::class)->find($gameId) : null;
            // If not, returns an error response
            if (!$game) {
                return $this->json("Ce jeu n'existe pas", Response::HTTP_BAD_REQUEST);
            }
            // Link the $game to the $character
            $character->setGame($game);
        }

        // Update the updatedAt field with the current date and time in the current game
        $character->getGame()->setUpdatedAt(new DateTimeImmutable());

        // Update the updatedAt field with the current date and time
        $character->setUpdatedAt(new DateTimeImmutable());

        // Edit the character in the DB
        $entityManager->flush();

        return $this->json($character,200,[], ["groups"=> ["character_edit"]]);
    }

    /**
    * Endpoint for deleting a character
    *
    * @Route("/{id}", name="deleteCharacters", requirements={"id"="\d+"}, methods={"DELETE"})
    */
    public function deleteCharacters($id, EntityManagerInterface $entityManager): JsonResponse
    {

        $character = $entityManager->getRepository(Character::class)->find($id);

        $this->denyAccessUnlessGranted('DELETE', $character);

        if ($character === null){
            return $this->json("Personnage introuvable avec cet ID :" . $id,Response::HTTP_NOT_FOUND);
        }

        $entityManager->getRepository(Character::class)->remove($character);
        $entityManager->flush();

        return $this->json("Personnage supprimé avec succès", Response::HTTP_OK);
    }
}

