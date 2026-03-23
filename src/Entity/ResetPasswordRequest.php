<?php

namespace App\Entity;

use App\Repository\ResetPasswordRequestRepository;
use Doctrine\ORM\Mapping as ORM;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestTrait;

/**
 * Entité représentant une demande de réinitialisation de mot de passe.
 *
 * Implémente {@see ResetPasswordRequestInterface} via le trait {@see ResetPasswordRequestTrait}
 * fourni par le bundle SymfonyCasts ResetPassword.
 *
 * Chaque demande génère un token sécurisé composé de :
 * - Un `selector` public (20 caractères) pour identifier la demande
 * - Un `hashedToken` stocké en base (haché pour la sécurité)
 *
 * Une demande est à usage unique et expire après 24 heures.
 * Elle est supprimée automatiquement après utilisation dans
 * {@see ResetPasswordController::reset()}.
 *
 * Sécurité : l'application ne révèle jamais si un email est enregistré
 * ou non lors d'une demande de réinitialisation (protection anti-énumération).
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[ORM\Entity(repositoryClass: ResetPasswordRequestRepository::class)]
class ResetPasswordRequest implements ResetPasswordRequestInterface
{
    use ResetPasswordRequestTrait;

    /**
     * Identifiant unique de la demande (clé primaire auto-incrémentée).
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Utilisateur ayant effectué la demande de réinitialisation.
     *
     * Non nullable : une demande est toujours liée à un utilisateur existant.
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Initialise la demande de réinitialisation avec les données du token.
     *
     * @param User               $user        L'utilisateur demandant la réinitialisation
     * @param \DateTimeInterface $expiresAt   date d'expiration du token (24h après création)
     * @param string             $selector    identifiant public du token (20 caractères)
     * @param string             $hashedToken token haché stocké en base de données
     */
    public function __construct(User $user, \DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $user;
        // Initialisation des champs du trait (selector, hashedToken, requestedAt, expiresAt)
        $this->initialize($expiresAt, $selector, $hashedToken);
    }

    /**
     * Retourne l'identifiant unique de la demande.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'utilisateur ayant effectué la demande.
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
