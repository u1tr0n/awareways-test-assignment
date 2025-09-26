<?php

namespace App\Repository;

use App\Entity\QuestionOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\UuidV7;

/**
 * @extends ServiceEntityRepository<QuestionOption>
 */
class QuestionOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionOption::class);
    }

    public function getNextSortPosition(UuidV7 $questionId): int
    {
        return (int)($this->createQueryBuilder('q')
            ->select('MAX(q.sortPosition) as cid')
            ->andWhere('q.question = :id')
            ->setParameter('id', $questionId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0) + 1;
    }

    public function getRefCount(UuidV7 $optionId): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select('count(q.id) as cid')
            ->andWhere('q.option = :id')
            ->setParameter('id', $optionId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
