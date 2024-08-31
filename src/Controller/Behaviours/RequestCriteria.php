<?php

declare(strict_types=1);

namespace App\Controller\Behaviours;

use Somnambulist\Bundles\ApiBundle\Request\SearchFormRequest;

trait RequestCriteria
{
    protected function requestCriteria(SearchFormRequest $request, $additionalCriteria = []): array
    {
        return array_merge($request->data()->prune()->without('page', 'per_page', 'order', 'include', 'qf')->all(), $additionalCriteria);
    }
}
