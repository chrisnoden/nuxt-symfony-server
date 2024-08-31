<?php declare(strict_types=1);

namespace App\Repository\Behaviours;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

trait Paginatable
{
    private function paginate(QueryBuilder $qb, int $page = 1, int $perPage = 30): Pagerfanta
    {
        return (new Pagerfanta(new QueryAdapter($qb)))->setMaxPerPage($perPage)->setCurrentPage($page);
    }
}
