<?php

namespace App\Repository;

use App\Entity\QuizQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\UuidV7;

/**
 * @extends ServiceEntityRepository<QuizQuestion>
 */
class QuizQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizQuestion::class);
    }

    public function getNextSortPosition(UuidV7 $quizVersionId): int
    {
        return (int)($this->createQueryBuilder('q')
            ->select('MAX(q.sortPosition) as cid')
            ->andWhere('q.quizVersion = :id')
            ->setParameter('id', $quizVersionId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0) + 1;
    }

    public function getRefCount(UuidV7 $questionId): int
    {
        return (int)$this->createQueryBuilder('q')
            ->select('count(q.id) as cid')
            ->andWhere('q.question = :id')
            ->setParameter('id', $questionId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
