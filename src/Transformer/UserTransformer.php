<?php declare(strict_types=1);

namespace App\Transformer;

use App\Entity\TwoFactorStatusType;
use App\Entity\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $entity): array
    {
        return [
            'id'               => $entity->getId(),
            'client'           => [
                'id'      => $entity->getClient()->getId(),
                'name'    => $entity->getClient()->getCompanyName(),
                'enabled' => $entity->getClient()->isEnabled(),
            ],
            'name'             => $entity->getName(),
            'email'            => $entity->getEmail(),
            'roles'            => $entity->getRoles(),
            'active'           => null !== $entity->getPassword(),
            'enabled'          => $entity->isEnabled(),
            'twoFactorEnabled' => $entity->isTwoFactorEnabled(),
            'twoFactorMethod' => $entity->getTwoFactorStatus()->value,
        ];
    }
}
