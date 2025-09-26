<?php

namespace App\Repository;

use App\Entity\QuizVersion;
use App\SemVer\SemVer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\UuidV7;

/**
 * @extends ServiceEntityRepository<QuizVersion>
 */
class QuizVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizVersion::class);
    }

    public function load(UuidV7 $id, ?string $version = null): ?QuizVersion
    {
        $qb = $this->createQueryBuilder('qv')
            ->leftJoin('qv.quiz', 'q', 'WITH', 'qv.quiz = q.id')
            ->andWhere('q.id = :id')
            ->setParameter('id', $id)
            ->orderBy('qv.version', 'DESC')
            ->setMaxResults(1)
        ;

        if (null !== $version) {
            $qb->andWhere('qv.version = :version')->setParameter('version', $version);
        }

        /** @var QuizVersion|null $result */
        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }

    public function getMaxVersion(UuidV7 $id): SemVer
    {
        $qb = $this->createQueryBuilder('qv')
            ->select('qv.version as version')
            // ->leftJoin('qv.quiz', 'q', 'WITH', 'qv.quiz = q.id')
            ->andWhere('qv.quiz = :id')
            ->setParameter('id', $id)
            ->orderBy('qv.version', 'DESC')
            ->setMaxResults(1)
        ;

        /** @var array{version: string} $result */
        $result = $qb->getQuery()->getSingleResult();

        return SemVer::fromString($result['version']);
    }
}
