<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserConfirmEmailRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidV7Generator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: UserConfirmEmailRepository::class)]
#[ORM\Table(name: 'user_confirm_email_requests')]
class UserConfirmEmail
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
    protected UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $email;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    protected ?DateTimeInterface $expiresAt = null;

    public function __construct(
        User $user,
        string $email,
    ) {
        $this->user = $user;
        $this->email = $email;
        $this->expiresAt = new DateTimeImmutable('+4 hours');
    }

    public function resetExpiry(): self
    {
        $this->expiresAt = new DateTimeImmutable('+4 hours');

        return $this;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }
}
