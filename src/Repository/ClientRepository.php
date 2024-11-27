<?php

namespace App\Repository;

use App\Entity\Client;
use App\Repository\Behaviours\Paginatable;
use App\Repository\Behaviours\WildcardSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use IlluminateAgnostic\Str\Support\Str;
use Pagerfanta\Pagerfanta;

class ClientRepository extends ServiceEntityRepository
{
    use Paginatable;
    use WildcardSearch;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Client $entity, bool $flush = false): void
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
    public function remove(Client $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByNameCaseInsensitive(string $companyName): ?Client
    {
        $qb = $this->createQueryBuilder('c');
        $qb->andWhere('
                ILIKE(c.companyName, :search) = true 
            ')
            ->setParameter('search', $companyName)
            ->setMaxResults(1)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function search(array $criteria = [], array $orderBy = [], int $page = 1, int $perPage = 100): Pagerfanta
    {
        $qb = $this->createQueryBuilder('c');

        if (isset($criteria['enabled'])) {
            $qb
                ->andWhere('c.enabled = :enabled')
                ->setParameter('enabled', $criteria['enabled'])
            ;
        }

        if (isset($criteria['name'])) {
            $qb->andWhere('
                ILIKE(c.companyName, :search) = true 
            ')
                ->setParameter('search', '%' . $this->spacesToWildcard($criteria['name']) . '%')
            ;
            unset($criteria['name']);
        }

        if (empty($orderBy)) {
            $qb->orderBy('c.id', 'asc');
        } else {
            foreach ($orderBy as $field => $order) {
                $qb->addOrderBy('c.'.Str::camel($field), $order);
            }
        }

        return $this->paginate($qb, $page, $perPage);
    }
}
