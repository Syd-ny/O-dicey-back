<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return User Returns a User object
    */
   public function loadUserByIdentifier(string $usernameOrEmail): ?User
   {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
                'SELECT u
                FROM App\Entity\User u
                WHERE u.login = :query
                OR u.email = :query'
            )
            ->setParameter('query', $usernameOrEmail)
            ->getOneOrNullResult();
   }

   public function loadUserByUsername($usernameOrEmail): ?User
   {
    
        return $this->loadUserByIdentifier($usernameOrEmail);
   }

   public function findBySearchUser($search, $sort, $order)
    {
        $queryBuilder = $this->createQueryBuilder('u');

        if (!empty($search)) {
            $queryBuilder->andWhere('u.email LIKE :search')
                ->setParameter('search','%' . $search . '%');
        }

        $queryBuilder-> orderBy('u.' . $sort, $order);

        return $queryBuilder->getQuery()->getResult();
    }
    
    
    public function findByIds(array $userIds): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id IN (:ids)')
            ->setParameter('ids', $userIds)
            ->getQuery()
            ->getResult();
    }

    public function findUserById($userId)
    {
        return $this->findOneBy(['id' => $userId]);
    }
}
