<?php

namespace App\Repository;

use App\Entity\ResetPasswordRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

/**
 * Repository des demandes de réinitialisation de mot de passe (ResetPasswordRequest).
 *
 * Implémente {@see ResetPasswordRequestRepositoryInterface} via le trait
 * {@see ResetPasswordRequestRepositoryTrait} fourni par le bundle SymfonyCasts ResetPassword.
 *
 * Le trait fournit automatiquement les méthodes nécessaires au bundle :
 * - Stockage du token en base de données
 * - Vérification de l'expiration
 * - Suppression après usage (token à usage unique)
 * - Nettoyage des tokens expirés
 *
 * La seule méthode à implémenter manuellement est `createResetPasswordRequest()`
 * qui instancie l'entité avec les paramètres du token.
 *
 * @extends ServiceEntityRepository<ResetPasswordRequest>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class ResetPasswordRequestRepository extends ServiceEntityRepository implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordRequest::class);
    }

    /**
     * Crée une nouvelle demande de réinitialisation de mot de passe.
     *
     * Méthode requise par {@see ResetPasswordRequestRepositoryInterface}.
     * Instancie l'entité {@see ResetPasswordRequest} avec les paramètres
     * du token générés par le bundle SymfonyCasts ResetPassword.
     *
     * @param User               $user        L'utilisateur demandant la réinitialisation
     * @param \DateTimeInterface $expiresAt   date d'expiration du token (24h après création)
     * @param string             $selector    identifiant public du token (20 caractères)
     * @param string             $hashedToken token haché stocké en base de données
     *
     * @return ResetPasswordRequestInterface la demande de réinitialisation créée
     */
    public function createResetPasswordRequest(
        object $user,
        \DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken,
    ): ResetPasswordRequestInterface {
        return new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);
    }
}
