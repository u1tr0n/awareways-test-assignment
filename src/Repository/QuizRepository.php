<?php

namespace App\Repository;

use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quiz>
 */
class QuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quiz::class);
    }

    /**
     * @return Collection<int, Quiz>
     */
    public function getPublishedList(): iterable
    {
        /** @var Collection<int, Quiz> $result */
        $result = $this->createQueryBuilder('q')
            ->orderBy('q.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
