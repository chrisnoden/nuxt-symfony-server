<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\Behaviours\ApplyCriteria;
use App\Repository\Behaviours\Paginatable;
use App\Repository\Behaviours\WildcardSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use IlluminateAgnostic\Str\Support\Str;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

use Symfony\Component\Security\Core\User\UserInterface;

use function array_key_exists;
use function get_class;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    use ApplyCriteria;
    use Paginatable;
    use WildcardSearch;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search(Client $client, array $criteria = [], array $orderBy = [], int $page = 1, int $perPage = 100): Pagerfanta
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.client', 'c')
        ;

        $joinedSorts = [
            'client_companyName' => 'c.companyName',
        ];

        if ($client->getId() > 1) {
            $qb
                ->andWhere('u.client = :client')
                ->setParameter('client', $client)
            ;

            if (isset($criteria['client'])) {
                unset($criteria['client']);
            }
        }

        if (isset($criteria['enabled'])) {
            $qb
                ->andWhere('u.enabled = :enabled')
                ->setParameter('enabled', $criteria['enabled'])
            ;
            unset($criteria['enabled']);
        }

        if (array_key_exists('q', $criteria) && !empty($criteria['q'])) {
            // this might be a slow search
            $qb->andWhere('
                ILIKE(u.name, :search) = true 
                OR ILIKE(u.email, :search) = true
            ')
                ->setParameter('search', '%' . $this->spacesToWildcard($criteria['q']) . '%')
            ;
            unset($criteria['q']);
        }

        if (array_key_exists('name', $criteria) && !empty($criteria['name'])) {
            // this might be a slow search
            $qb->andWhere('ILIKE(u.name, :name) = true ')
                ->setParameter('name', $this->wildcard(strtolower($criteria['name'])))
            ;

            unset($criteria['name']);
        }

        if (array_key_exists('email', $criteria) && !empty($criteria['email'])) {
            // this might be a slow search
            $qb->andWhere('ILIKE(u.email, :email) = true ')
                ->setParameter('email', $this->wildcard(strtolower($criteria['email'])))
            ;

            unset($criteria['email']);
        }

        if (array_key_exists('role', $criteria) && !empty($criteria['role'])) {
            $qb->andWhere('JSONB_CONTAINS(u.roles, \'["' . strtoupper($criteria['role']) . '"]\') = true');

            unset($criteria['role']);
        }

        $qb = $this->applyCriteria($qb, $criteria);
        $qb = $this->applyOrderBy($qb, 'u', $orderBy, $joinedSorts);

        return $this->paginate($qb, $page, $perPage);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->findOneBy(['email' => Str::lower($identifier)]);
    }
}
