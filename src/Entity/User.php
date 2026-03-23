<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Entité représentant un utilisateur de l'application (User).
 *
 * Implémente {@see UserInterface} et {@see PasswordAuthenticatedUserInterface}
 * pour l'intégration avec le système de sécurité Symfony.
 *
 * Trois rôles applicatifs sont définis :
 * - `ROLE_ADMIN` : accès complet (gestion utilisateurs, partenaires, référentiels)
 * - `ROLE_COLLABORATOR` : accès stocks, commandes et tableau de bord
 * - `ROLE_PARTNER` : espace restreint (stock virtuel personnel + réservations)
 *
 * Tout utilisateur hérite automatiquement de `ROLE_USER` (comportement Symfony standard).
 *
 * Mécanismes de sécurité spécifiques :
 * - `mustChangePassword` : force le changement de mot de passe à la prochaine connexion (RG-15).
 * - `isActive` : permet de désactiver un compte sans le supprimer.
 * - Le mot de passe est haché via bcrypt et sérialisé en CRC32C côté session (Symfony 7.3+).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * Identifiant unique de l'utilisateur (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Adresse email de l'utilisateur.
     *
     * Sert d'identifiant unique de connexion (contrainte UNIQUE en base).
     * Validé par {@see UniqueEntity} au niveau de l'application.
     */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * Tableau des rôles Symfony de l'utilisateur.
     *
     * Valeurs applicatives : ROLE_ADMIN, ROLE_COLLABORATOR, ROLE_PARTNER.
     * ROLE_USER est ajouté automatiquement dans {@see getRoles()}.
     * Stocké en JSON en base de données.
     *
     * @var list<string>
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe haché de l'utilisateur.
     *
     * Haché via bcrypt par Symfony Security.
     * Sérialisé en CRC32C côté session depuis Symfony 7.3 pour éviter
     * que le hash réel ne circule en session.
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Nom de famille de l'utilisateur.
     */
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    /**
     * Prénom de l'utilisateur.
     */
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    /**
     * Statut actif/inactif du compte utilisateur.
     *
     * `false` = compte désactivé sans suppression.
     * Un compte inactif ne peut pas se connecter à l'application.
     */
    #[ORM\Column]
    private ?bool $isActive = null;

    /**
     * Indicateur de changement de mot de passe obligatoire.
     *
     * Mis à `true` par l'admin lors d'une réinitialisation forcée (RG-15).
     * Remis à `false` automatiquement après le changement par l'utilisateur.
     * Tant que `true`, l'utilisateur est redirigé vers la page de changement.
     */
    #[ORM\Column]
    private bool $mustChangePassword = false;

    /**
     * Commandes dont cet utilisateur est le dernier modificateur.
     *
     * Relation inverse de Order::updated_by.
     *
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'updated_by')]
    private Collection $userOrders;

    /**
     * Partenaire fournisseur associé à cet utilisateur.
     *
     * `NULL` pour les collaborateurs et administrateurs internes.
     * Renseigné pour les utilisateurs avec le rôle ROLE_PARTNER.
     */
    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Partner $partner = null;

    /**
     * Historique des changements de statut effectués par cet utilisateur.
     *
     * @var Collection<int, OrderStatusHistory>
     */
    #[ORM\OneToMany(targetEntity: OrderStatusHistory::class, mappedBy: 'changedBy')]
    private Collection $orderStatusHistories;

    /**
     * Commandes créées par cet utilisateur (en tant que collaborateur).
     *
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'collaborator')]
    private Collection $orders;

    /**
     * Stocks dont cet utilisateur est le dernier modificateur.
     *
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'updated_by')]
    private Collection $stocks;

    /**
     * Initialise les collections orders, stocks, userOrders et orderStatusHistories.
     */
    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->userOrders = new ArrayCollection();
        $this->orderStatusHistories = new ArrayCollection();
    }

    /**
     * Retourne l'identifiant unique de l'utilisateur.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'adresse email de l'utilisateur.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse email de l'utilisateur.
     *
     * @param string $email adresse email unique dans le système
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Retourne l'identifiant visuel de l'utilisateur (email).
     *
     * Utilisé par Symfony Security comme identifiant de session.
     *
     * @see UserInterface
     *
     * @return string L'email de l'utilisateur
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Retourne les rôles de l'utilisateur.
     *
     * Garantit que chaque utilisateur possède au moins ROLE_USER
     * (comportement standard Symfony). Les doublons sont supprimés.
     *
     * @see UserInterface
     *
     * @return list<string> tableau des rôles sans doublons
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Garantit que tout utilisateur a au minimum ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur.
     *
     * @param list<string> $roles tableau de rôles (ex : ['ROLE_ADMIN', 'ROLE_COLLABORATOR'])
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Retourne le mot de passe haché de l'utilisateur.
     *
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe haché de l'utilisateur.
     *
     * Le mot de passe doit être haché avant d'appeler cette méthode
     * via {@see UserPasswordHasherInterface::hashPassword()}.
     *
     * @param string $password mot de passe haché (bcrypt)
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Sérialise l'utilisateur pour la session en remplaçant le hash par son CRC32C.
     *
     * Depuis Symfony 7.3, le hash du mot de passe est remplacé par son CRC32C
     * en session pour éviter que le hash bcrypt réel ne soit exposé.
     *
     * @return array<string, mixed> données de sérialisation avec le hash CRC32C
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    /**
     * Efface les données sensibles temporaires de l'utilisateur.
     *
     * @deprecated méthode dépréciée depuis Symfony 7, à supprimer lors de la migration vers Symfony 8
     */
    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, à supprimer lors de la migration vers Symfony 8
    }

    /**
     * Retourne le nom de famille de l'utilisateur.
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Définit le nom de famille de l'utilisateur.
     */
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Retourne le prénom de l'utilisateur.
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Définit le prénom de l'utilisateur.
     */
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Retourne si le compte est actif.
     *
     * @return bool|null `true` = compte actif, `false` = compte désactivé
     */
    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * Définit le statut actif/inactif du compte.
     *
     * @param bool $isActive `true` pour activer, `false` pour désactiver sans suppression
     */
    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Retourne la collection des commandes dont l'utilisateur est le dernier modificateur.
     *
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * Ajoute une commande à cet utilisateur (en tant que collaborateur créateur).
     *
     * @param Order $order la commande à associer
     */
    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCollaborator($this);
        }

        return $this;
    }

    /**
     * Retire une commande de la liste des commandes créées par cet utilisateur.
     *
     * @param Order $order la commande à dissocier
     */
    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getCollaborator() === $this) {
                $order->setCollaborator(null);
            }
        }

        return $this;
    }

    /**
     * Retourne la collection des stocks dont cet utilisateur est le dernier modificateur.
     *
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * Ajoute un stock à la liste des stocks modifiés par cet utilisateur.
     *
     * @param Stock $stock le stock à associer
     */
    public function addStock(Stock $stock): static
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setUpdatedBy($this);
        }

        return $this;
    }

    /**
     * Retire un stock de la liste des stocks modifiés par cet utilisateur.
     *
     * @param Stock $stock le stock à dissocier
     */
    public function removeStock(Stock $stock): static
    {
        if ($this->stocks->removeElement($stock)) {
            if ($stock->getUpdatedBy() === $this) {
                $stock->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * Retourne si l'utilisateur doit obligatoirement changer son mot de passe.
     *
     * @return bool|null `true` = changement obligatoire à la prochaine connexion (RG-15)
     */
    public function isMustChangePassword(): ?bool
    {
        return $this->mustChangePassword;
    }

    /**
     * Définit l'obligation de changement de mot de passe.
     *
     * Mis à `true` par l'admin lors d'une réinitialisation forcée.
     * Remis à `false` automatiquement après le changement par l'utilisateur.
     */
    public function setMustChangePassword(bool $mustChangePassword): static
    {
        $this->mustChangePassword = $mustChangePassword;

        return $this;
    }

    /**
     * Retourne les commandes dont cet utilisateur est le dernier modificateur.
     *
     * @return Collection<int, Order>
     */
    public function getUserOrders(): Collection
    {
        return $this->userOrders;
    }

    /**
     * Ajoute une commande à la liste des commandes modifiées par cet utilisateur.
     *
     * @param Order $userOrder la commande à associer
     */
    public function addUserOrder(Order $userOrder): static
    {
        if (!$this->userOrders->contains($userOrder)) {
            $this->userOrders->add($userOrder);
            $userOrder->setUpdatedBy($this);
        }

        return $this;
    }

    /**
     * Retire une commande de la liste des commandes modifiées par cet utilisateur.
     *
     * @param Order $userOrder la commande à dissocier
     */
    public function removeUserOrder(Order $userOrder): static
    {
        if ($this->userOrders->removeElement($userOrder)) {
            if ($userOrder->getUpdatedBy() === $this) {
                $userOrder->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * Retourne le partenaire associé à cet utilisateur.
     *
     * @return Partner|null null pour les collaborateurs et admins internes
     */
    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    /**
     * Définit le partenaire associé à cet utilisateur.
     *
     * Doit être renseigné pour les utilisateurs avec le rôle ROLE_PARTNER.
     *
     * @param Partner|null $partner le partenaire à associer, ou null pour usage interne
     */
    public function setPartner(?Partner $partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * Retourne la collection des historiques de statut effectués par cet utilisateur.
     *
     * @return Collection<int, OrderStatusHistory>
     */
    public function getOrderStatusHistories(): Collection
    {
        return $this->orderStatusHistories;
    }

    /**
     * Ajoute une entrée d'historique de statut à cet utilisateur.
     *
     * @param OrderStatusHistory $orderStatusHistory L'entrée d'historique à associer
     */
    public function addOrderStatusHistory(OrderStatusHistory $orderStatusHistory): static
    {
        if (!$this->orderStatusHistories->contains($orderStatusHistory)) {
            $this->orderStatusHistories->add($orderStatusHistory);
            $orderStatusHistory->setChangedBy($this);
        }

        return $this;
    }

    /**
     * Retire une entrée d'historique de statut de cet utilisateur.
     *
     * @param OrderStatusHistory $orderStatusHistory L'entrée à dissocier
     */
    public function removeOrderStatusHistory(OrderStatusHistory $orderStatusHistory): static
    {
        if ($this->orderStatusHistories->removeElement($orderStatusHistory)) {
            if ($orderStatusHistory->getChangedBy() === $this) {
                $orderStatusHistory->setChangedBy(null);
            }
        }

        return $this;
    }
}
