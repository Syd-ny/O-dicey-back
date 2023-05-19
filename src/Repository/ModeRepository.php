<?php

namespace App\Repository;

use App\Entity\Mode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mode>
 *
 * @method Mode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mode[]    findAll()
 * @method Mode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mode::class);
    }

    public function add(Mode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Mode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findBySearch($search, $sort, $order)
    {
        $queryBuilder = $this->createQueryBuilder('u');

        if (!empty($search)) {
            $queryBuilder->andWhere('u.name LIKE :search')
                ->setParameter('search','%' . $search . '%');
        }

        $queryBuilder-> orderBy('u.' . $sort, $order);

        return $queryBuilder->getQuery()->getResult();
    }

//    /**
//     * @return Mode[] Returns an array of Mode objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mode
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
