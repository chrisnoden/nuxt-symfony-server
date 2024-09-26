<?php declare(strict_types=1);

namespace App\Transformer;

use App\Entity\Client;
use League\Fractal\TransformerAbstract;

class ClientTransformer extends TransformerAbstract
{
    public function transform(Client $entity): array
    {
        return [
            'id'          => $entity->getId(),
            'companyName' => $entity->getCompanyName(),
            'enabled'     => $entity->isEnabled(),
        ];
    }
}
