<?php declare(strict_types=1);

namespace App\Repository\Criteria;

use Doctrine\ORM\QueryBuilder;

use function array_key_exists;

abstract class AbstractCriteria implements QueryBuilderCriteria
{
    protected array $fields = [];

    public function canBeApplied(QueryBuilder $qb, array $values): bool
    {
        $valid = !empty($this->fields);

        foreach ($this->fields as $field) {
            $valid = $valid && array_key_exists($field, $values);
        }

        return $valid;
    }

    public abstract function apply(QueryBuilder $qb, array $values): void;

    protected function getValuesFromArray(array $fields, array $values): array
    {
        $ret = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $values)) {
                $ret[] = $values[$field];
            }
        }

        return $ret;
    }

    protected function getFirstValueFromArray(array $fields, array $values): mixed
    {
        return $this->getValuesFromArray($fields, $values)[0] ?? null;
    }
}
