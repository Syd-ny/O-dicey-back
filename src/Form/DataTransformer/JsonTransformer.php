<?php

namespace App\Form\DataTransformer;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Handles transforming json to array and backward
 */
class JsonTransformer implements DataTransformerInterface
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Character::class);
    }
    
    /**
     * Transforms a JSON into an array
     *
     * @param  Json $stats
     * @throws TransformationFailedException if json stats are not found
     */
    public function transform($character): mixed
    {
        if (empty($character->getStats())) {
            return null;
        }
        $stats = $character->getStats();
        return json_decode($stats, true);
    }

    /**
     * Transforms an array into a Json
     *
     * @param mixed $stats
     */
    public function reverseTransform($stats): mixed
    {
        if (empty($stats)) {
            return json_encode([]);
        }

        return json_encode($stats);
    }
}