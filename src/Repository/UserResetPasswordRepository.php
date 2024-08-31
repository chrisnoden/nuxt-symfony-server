<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

/**
 * @extends ServiceEntityRepository<UserResetPassword>
 *
 * @method UserResetPassword|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserResetPassword|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserResetPassword[]    findAll()
 * @method UserResetPassword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserResetPasswordRepository extends ServiceEntityRepository implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserResetPassword::class);
    }

    public function add(UserResetPassword $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserResetPassword $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function createResetPasswordRequest(object $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken): ResetPasswordRequestInterface
    {
        return new UserResetPassword($user, $expiresAt, $selector, $hashedToken);
    }
}
