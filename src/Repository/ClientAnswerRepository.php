<?php

namespace App\Repository;

use App\Entity\ClientAnswer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\UuidV7;

/**
 * @extends ServiceEntityRepository<ClientAnswer>
 */
class ClientAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClientAnswer::class);
    }

    public function getAnswersCount(UuidV7 $clientId): int
    {
        return (int)($this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.question) as cid')
            ->andWhere('c.client = :id')
            ->setParameter('id', $clientId)
            //->groupBy('c.question')
            ->getQuery()
            ->getSingleScalarResult() ?? 0);
    }

    //    /**
    //     * @return ClientAnswer[] Returns an array of ClientAnswer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ClientAnswer
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
