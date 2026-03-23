<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Écouteur d'événement vérifiant l'état du compte à chaque requête.
 *
 * S'abonne à l'événement {@see KernelEvents::REQUEST} pour intercepter
 * chaque requête principale et effectuer deux vérifications :
 *
 * 1. **Compte désactivé** : si un utilisateur connecté a son compte désactivé
 *    (isActive = false), sa session est invalidée et il est redirigé vers la
 *    page de connexion. Ce cas peut survenir si l'admin désactive un compte
 *    pendant une session active.
 *
 * 2. **Changement de mot de passe obligatoire** : si le flag `mustChangePassword`
 *    est activé (suite à une réinitialisation admin - RG-15), l'utilisateur est
 *    redirigé vers la page de changement de mot de passe avant toute navigation.
 *
 * Routes exclues des vérifications pour éviter les boucles de redirection :
 * - `app_user_change_password` : la page de changement de mot de passe elle-même
 * - `app_login` : la page de connexion
 * - `app_logout` : la déconnexion
 *
 * @author CASTELLS Cyprien
 * @version 1.2
 */
class CheckPasswordChangeSubscriber implements EventSubscriberInterface
{
    /**
     * Initialise le subscriber avec les dépendances nécessaires.
     *
     * @param Security              $security     Service de sécurité Symfony pour accéder à l'utilisateur connecté.
     * @param UrlGeneratorInterface $urlGenerator Générateur d'URL pour les redirections.
     */
    public function __construct(
        private Security $security,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * Intercepte chaque requête principale pour vérifier l'état du compte utilisateur.
     *
     * Ignoré pour les requêtes secondaires (sous-requêtes Symfony internes)
     * et pour les routes exclues afin d'éviter toute boucle infinie.
     *
     * @param RequestEvent $event L'événement de requête Symfony.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // Ignorer les sous-requêtes Symfony (ex : render() dans Twig)
        if (!$event->isMainRequest()) {
            return;
        }

        $routeName = $event->getRequest()->attributes->get('_route');

        // Routes exclues : éviter les boucles de redirection infinies
        if (in_array($routeName, ['app_user_change_password', 'app_login', 'app_logout'])) {
            return;
        }

        /** @var User|null $user */
        $user = $this->security->getUser();

        // Aucune vérification si l'utilisateur n'est pas connecté
        if (!$user instanceof User) {
            return;
        }

        // Vérification 1 : Compte désactivé pendant une session active
        // L'admin peut désactiver un compte même si l'utilisateur est connecté
        if (!$user->isActive()) {
            // Invalidation de la session et redirection vers le login
            $this->security->logout(false);
            $response = new RedirectResponse($this->urlGenerator->generate('app_login'));
            $event->setResponse($response);

            return;
        }

        // Vérification 2 : Changement de mot de passe obligatoire (RG-15)
        // Activé par l'admin lors d'une réinitialisation forcée
        if ($user->isMustChangePassword()) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_user_change_password'));
            $event->setResponse($response);
        }
    }

    /**
     * Retourne les événements auxquels ce subscriber est abonné.
     *
     * S'abonne à {@see KernelEvents::REQUEST} pour intercepter toutes
     * les requêtes entrantes avant qu'elles n'atteignent le contrôleur.
     *
     * @return array<string, string> Tableau [événement => méthode].
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}