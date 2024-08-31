<?php

declare(strict_types=1);

namespace App\Repository\Behaviours;

trait WildcardSearch
{
    protected function wildcard(string $search): string
    {
        return '%' . $this->spacesToWildcard($search) . '%';
    }

    protected function spacesToWildcard(string $search): string
    {
        return preg_replace('/\s+/', '%', trim($search));
    }
}
