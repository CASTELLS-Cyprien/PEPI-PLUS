<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Authentificateur personnalisé pour la connexion par formulaire.
 *
 * Étend {@see AbstractLoginFormAuthenticator} pour gérer l'authentification
 * par email et mot de passe dans Pépi+.
 *
 * Badges Symfony Security utilisés :
 * - {@see UserBadge} : récupération de l'utilisateur par email
 * - {@see PasswordCredentials} : vérification du mot de passe haché
 * - {@see CsrfTokenBadge} : protection CSRF du formulaire de connexion
 * - {@see RememberMeBadge} : support de la case "Se souvenir de moi"
 *
 * Après une authentification réussie :
 * - Si une URL cible était en session (redirection avant login), l'utilisateur y est renvoyé.
 * - Sinon, redirection vers le tableau de bord (`app_dashboard`).
 *
 * @author CASTELLS Cyprien
 * @version 1.2
 */
class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    /**
     * Nom de la route de connexion.
     *
     * Utilisé par {@see AbstractLoginFormAuthenticator} pour générer
     * l'URL de redirection vers la page de login.
     */
    public const LOGIN_ROUTE = 'app_login';

    /**
     * Initialise l'authentificateur avec le générateur d'URL.
     *
     * @param UrlGeneratorInterface $urlGenerator Générateur d'URL Symfony.
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Crée le passeport d'authentification à partir de la requête de connexion.
     *
     * Extrait l'email et le mot de passe depuis le payload de la requête POST.
     * Stocke le dernier email saisi en session pour pré-remplissage en cas d'erreur.
     *
     * @param Request $request La requête HTTP de connexion.
     *
     * @return Passport Le passeport contenant les badges d'authentification.
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        // Sauvegarde du dernier email saisi pour pré-remplissage du formulaire en cas d'erreur
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                // Protection CSRF du formulaire de connexion
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                // Support de la case "Se souvenir de moi"
                new RememberMeBadge(),
            ]
        );
    }

    /**
     * Gère la redirection après une authentification réussie.
     *
     * Si une URL cible est en session (l'utilisateur était redirigé vers le login
     * depuis une page protégée), il est renvoyé vers cette URL.
     * Sinon, redirection vers le tableau de bord de l'application.
     *
     * @param Request        $request      La requête HTTP.
     * @param TokenInterface $token        Le token d'authentification Symfony.
     * @param string         $firewallName Nom du pare-feu Symfony configuré.
     *
     * @return Response|null Réponse de redirection ou null pour continuer la requête.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirection vers l'URL d'origine si l'utilisateur était en cours de navigation
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirection par défaut vers le tableau de bord après connexion réussie
        return new RedirectResponse($this->urlGenerator->generate('app_dashboard'));
    }

    /**
     * Retourne l'URL de la page de connexion.
     *
     * Utilisé par {@see AbstractLoginFormAuthenticator} pour rediriger
     * les utilisateurs non connectés vers le formulaire de login.
     *
     * @param Request $request La requête HTTP.
     *
     * @return string L'URL de la page de connexion.
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}