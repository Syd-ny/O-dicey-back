<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function add(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Game $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByName($name)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }

    public function findBySearchGame($search, $sort, $order)
    {
        $queryBuilder = $this->createQueryBuilder('u');

        if (!empty($search)) {
            $queryBuilder->andWhere('u.name LIKE :search')
                ->setParameter('search','%' . $search . '%');
        }

        $queryBuilder-> orderBy('u.' . $sort, $order);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findGamesWithoutCharacters()
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.characters', 'c')
            ->where('c.id IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findGamesWithoutCharactersForDm(User $user): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.characters', 'c')
            ->where('c.id IS NULL')
            ->andWhere('g.dm = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findGamesWithoutCharactersForUser(User $user): array
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.characters', 'c')
            ->where('c.id IS NULL')
            ->andWhere(':user MEMBER OF g.gameUsers')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
