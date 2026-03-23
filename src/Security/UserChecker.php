<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Vérificateur d'état du compte utilisateur lors de la connexion.
 *
 * Implémente {@see UserCheckerInterface} pour ajouter des vérifications
 * métier personnalisées avant et après l'authentification Symfony.
 *
 * Vérification implémentée :
 * - **Pre-auth** : vérifie que le compte est actif (`isActive = true`).
 *   Si le compte est désactivé, une exception avec un message explicite est levée.
 *   Cela empêche la connexion sans exposer d'informations sensibles (pas de
 *   "mauvais mot de passe" vs "compte désactivé" via les exceptions standard).
 *
 * Cette classe est déclarée comme service dans la configuration du pare-feu
 * Symfony (`security.yaml`) via la clé `user_checker`.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * Vérifications effectuées AVANT la validation du mot de passe.
     *
     * Vérifie que le compte utilisateur est actif.
     * Cette vérification est faite en pré-authentification pour éviter
     * une vérification inutile du mot de passe sur un compte désactivé.
     *
     * @param UserInterface $user L'utilisateur tentant de se connecter
     *
     * @throws CustomUserMessageAuthenticationException si le compte est désactivé
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            // Pas une instance de notre entité User, on laisse Symfony gérer
            return;
        }

        if (!$user->isActive()) {
            // Lève une exception avec un message clair pour l'utilisateur
            throw new CustomUserMessageAuthenticationException('Votre compte est désactivé. Veuillez contacter un administrateur.');
        }
    }

    /**
     * Vérifications effectuées APRÈS la validation du mot de passe.
     *
     * Aucune vérification supplémentaire post-authentification dans Pépi+.
     * Cette méthode est requise par l'interface mais laissée vide intentionnellement.
     *
     * @param UserInterface $user L'utilisateur authentifié
     */
    public function checkPostAuth(UserInterface $user): void
    {
        // Aucune vérification post-authentification requise dans Pépi+
    }
}
