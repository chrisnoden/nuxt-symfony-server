<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserConfirmEmail;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserConfirmEmail>
 *
 * @method UserConfirmEmail|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserConfirmEmail|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserConfirmEmail[]    findAll()
 * @method UserConfirmEmail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserConfirmEmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserConfirmEmail::class);
    }

    public function save(UserConfirmEmail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserConfirmEmail $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function expireOldRequests(): void
    {
        $this
            ->createQueryBuilder('uce')
            ->delete()
            ->where('uce.expiresAt <= :now')
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->execute()
        ;
    }
}
