<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Question;
use App\Entity\QuizQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\UuidV7;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function getClientQuestion(UuidV7 $clientId, int $offset): ?Question
    {
        /** @var ?Question $result */
        $result = $this->createQueryBuilder('q')
            ->leftJoin(QuizQuestion::class, 'qq', 'WITH', 'q.id = qq.question')
            ->leftJoin(Client::class, 'c', 'WITH', 'qq.quizVersion = c.quiz')
            ->andWhere('c.id = :id')
            ->setParameter('id', $clientId)
            ->orderBy('qq.sortPosition', 'ASC')
            ->setMaxResults(1)
            ->setFirstResult($offset)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    public function getClientQuestionsCount(UuidV7 $clientId): int
    {
        return (int)($this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->leftJoin(QuizQuestion::class, 'qq', 'WITH', 'q.id = qq.question')
            ->leftJoin(Client::class, 'c', 'WITH', 'qq.quizVersion = c.quiz')
            ->andWhere('c.id = :id')
            ->setParameter('id', $clientId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0);
    }
}
