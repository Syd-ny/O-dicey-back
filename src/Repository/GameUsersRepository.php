<?php

namespace App\Repository;

use App\Entity\GameUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameUsers>
 *
 * @method GameUsers|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameUsers|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameUsers[]    findAll()
 * @method GameUsers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameUsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameUsers::class);
    }

    public function add(GameUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GameUsers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
