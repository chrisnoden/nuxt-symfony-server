<?php declare(strict_types=1);

namespace App\Repository\Criteria;

use Doctrine\ORM\QueryBuilder;

interface QueryBuilderCriteria
{
    public function canBeApplied(QueryBuilder $qb, array $values): bool;

    public function apply(QueryBuilder $qb, array $values): void;
}
