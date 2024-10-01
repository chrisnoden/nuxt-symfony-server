<?php

declare(strict_types=1);

namespace App\Repository\Behaviours;

use App\Repository\Criteria\AbstractCriteria;
use App\Repository\Criteria\ApplicableCriteria;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use IlluminateAgnostic\Str\Support\Str;

trait ApplyCriteria
{
    protected function applyCriteria(QueryBuilder $qb, array $criteria = []): QueryBuilder
    {
        $abstractCriteria = [];
        if (!empty($criteria)) {
            $newCriteria = new Criteria();
            foreach ($criteria as $fieldName => $value) {
                if ($value instanceof AbstractCriteria) {
                    $abstractCriteria[] = $value;
                } else {
                    $newCriteria->andWhere(
                        Criteria::expr()->eq(Str::camel($fieldName), $value)
                    );
                }
            }

            $qb->addCriteria($newCriteria);

            if (!empty($abstractCriteria)) {
                $toApply = new ApplicableCriteria(...$abstractCriteria);
                $toApply($qb, []);
            }
        }

        return $qb;
    }

    protected function applyOrderBy(QueryBuilder $qb, string $alias, array $orderBy = [], array $joinedSorts = []): QueryBuilder
    {
        $classMeta = $this->getEntityManager()->getClassMetadata($this->getClassName());

        if (!empty($orderBy)) {
            foreach ($orderBy as $fieldName => $order) {
                if (in_array($fieldName, $classMeta->fieldNames)) {
                    $qb->addOrderBy('lower('.$alias . '.' . $fieldName.')', $order);
                } elseif (in_array(Str::camel($fieldName), $classMeta->fieldNames)) {
                    $qb->addOrderBy('lower('.$alias . '.' . Str::camel($fieldName).')', $order);
                } elseif (isset($joinedSorts[$fieldName])) {
                    $qb->addOrderBy('lower('.$joinedSorts[$fieldName].')', $order);
                }
            }
        }

        return $qb;
    }
}
