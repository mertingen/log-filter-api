<?php

namespace App\Repository;

use App\Entity\ServiceHttpLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceHttpLog>
 *
 * @method ServiceHttpLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceHttpLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceHttpLog[]    findAll()
 * @method ServiceHttpLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceHttpLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceHttpLog::class);
    }

//    /**
//     * @return ServiceHttpLog[] Returns an array of ServiceHttpLog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ServiceHttpLog
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
