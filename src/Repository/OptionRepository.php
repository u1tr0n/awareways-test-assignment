<?php

namespace App\Repository;

use App\Entity\Option;
use App\Entity\QuestionOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\UuidV7;

/**
 * @extends ServiceEntityRepository<Option>
 */
class OptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Option::class);
    }

    /**
     * @return array<int, Option>
     */
    public function getQuestionOptions(UuidV7 $questionId): array
    {
        /** @var array<int, Option> $result */
        $result = $this->createQueryBuilder('o')
            ->select('o')
            ->leftJoin(QuestionOption::class, 'qo', 'WITH', 'o.id = qo.option')
            ->andWhere('qo.question = :id')
            ->setParameter('id', $questionId)
            ->orderBy('qo.sortPosition', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
