<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use IlluminateAgnostic\Str\Support\Str;
use Ramsey\Uuid\Doctrine\UuidV7Generator;
use Ramsey\Uuid\UuidInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as GoogleTwoFactorInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface as EmailTwoFactorInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EmailTwoFactorInterface, GoogleTwoFactorInterface, EquatableInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidV7Generator::class)]
    protected ?UuidInterface $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json', options: ['jsonb' => true])]
    private array $roles = [];

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $password = null;

    /**
     * The plain non-persisted password
     */
    private ?string $plainPassword = null;

    #[ORM\Column]
    private bool $enabled = true;

    #[ORM\Column(type: 'string', length: 20, enumType: TwoFactorStatusType::class)]
    private TwoFactorStatusType $twoFactorStatus = TwoFactorStatusType::DISABLED;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $googleAuthenticatorSecret = null;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private ?string $authCode = null;

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = Str::lower($email);

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles   = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {

    }

    public function isEmailAuthEnabled(): bool
    {
        return $this->twoFactorStatus === TwoFactorStatusType::MAGIC_LINK;
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->isGoogleAuthenticatorEnabled() || $this->isEmailAuthEnabled();
    }

    public function isGoogleAuthenticatorEnabled(): bool
    {
        return null !== $this->googleAuthenticatorSecret && $this->twoFactorStatus === TwoFactorStatusType::GOOGLE_AUTHENTICATOR;
    }

    public function getTwoFactorStatus(): TwoFactorStatusType
    {
        return $this->twoFactorStatus;
    }

    public function setTwoFactorStatus(TwoFactorStatusType $twoFactorStatus): self
    {
        $this->twoFactorStatus = $twoFactorStatus;

        return $this;
    }

    public function getGoogleAuthenticatorUsername(): string
    {
        return $this->email;
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->googleAuthenticatorSecret;
    }

    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): self
    {
        $this->googleAuthenticatorSecret = $googleAuthenticatorSecret;

        return $this;
    }

    public function asResponseArray(): array
    {
        return [
            'id'               => $this->getId(),
            'name'             => $this->getName(),
            'email'            => $this->getEmail(),
            'roles'            => $this->getRoles(),
            'twoFactorEnabled' => $this->isGoogleAuthenticatorEnabled(),
        ];
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->enabled !== $user->enabled || !$user->getClient()->isEnabled()) {
            // Forces the user to log in again
            return false;
        }

        return true;
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException('The email authentication code was not set');
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(?string $authCode): void
    {
        $this->authCode = $authCode;
    }
}
