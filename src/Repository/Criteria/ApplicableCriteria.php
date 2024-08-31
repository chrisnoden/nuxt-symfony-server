<?php declare(strict_types=1);

namespace App\Repository\Criteria;

use Doctrine\ORM\QueryBuilder;

class ApplicableCriteria
{
    private array $criteria;

    public function __construct(QueryBuilderCriteria ...$criteria)
    {
        $this->criteria = $criteria;
    }

    public function __invoke(QueryBuilder $qb, array $values): void
    {
        foreach ($this->criteria as $criterion) {
            if ($criterion->canBeApplied($qb, $values)) {
                $criterion->apply($qb, $values);
            }
        }
    }
}
