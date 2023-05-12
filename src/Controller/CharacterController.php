<?php

namespace App\Controller;


use App\Entity\Character;
use App\Repository\CharacterRepository;
use App\Repository\User;
use App\Repository\Game;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
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
 * @Route("/api/characters", name="app_api_characters_")
 */
class CharacterController extends AbstractController
{

    /**
    * @Route("", name="browse", methods={"GET"})
    */
    public function browse(CharacterRepository $characterRepository): JsonResponse
    {
        $characters = $characterRepository->findAll();

        if ($characters === null){return $this->json("aucun joueur trouvé", Response::HTTP_NOT_FOUND);}

        return $this->json($characters,200,[], ["groups"=> ["character_list"]]);
    }

    /**
    * @Route("/{id}", name="read", methods={"GET"})
    */
    public function read(Character $character): JsonResponse
    {

        if ($character === null){return $this->json("ce joueur n'existe pas", Response::HTTP_NOT_FOUND);}

        return $this->json($character,200,[], ["groups"=> ["character_read"]]);
    }

    /**
    * @Route("", name="add", methods={"POST"})
    */
    public function add(
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
        //dd($character);

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
    * @Route("/{id}", name="edit", requirements={"id"="\d+"}, methods={"PUT","PATCH"})
    */
    public function edit(
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
        if (!$character) {
            return $this->json("Le personnage n'existe pas.", Response::HTTP_NOT_FOUND);
        }
    

        // we get the object
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

        $data = json_decode($jsonContent, true);
        $userId = $data["user"] ?? null;
        $user = $userId ? $userRepository->find($userId) : null;

        if (!$user) {
            return $this->json("Cet utilisateur n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        $gameId = $data["game"] ?? null;
        $game = $userId ? $gameRepository->find($gameId) : null;
    
        if (!$game) {
            return $this->json("Ce jeu n'existe pas", Response::HTTP_BAD_REQUEST);
        }

        $character->setUser($user);
        $character->setGame($game);

        $entityManager->flush();

        return $this->json($character,200,[], ["groups"=> ["character_edit"]]);
    }

    /**
    * @Route("/{id}", name="delete", requirements={"id"="\d+"}, methods={"DELETE"})
    */
    public function delete(
        $id,
        CharacterRepository $characterRepository,
        EntityManagerInterface $entityManager): JsonResponse
    {

        $character = $characterRepository->find($id);

        if ($character === null){
            return $this->json("personnage introuvable avec cet ID :" . $id,Response::HTTP_NOT_FOUND);
        }

        $characterRepository->remove($character);
        $entityManager->flush();

        return $this->json("personnage supprimé avec succès", Response::HTTP_OK);
    }
}

