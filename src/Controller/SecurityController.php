<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur de sécurité gérant la connexion et la déconnexion.
 *
 * La connexion est gérée via le formulaire Symfony Security avec
 * authentification par email et mot de passe.
 *
 * La déconnexion est interceptée par le pare-feu Symfony (firewall)
 * avant que la méthode ne soit exécutée : la méthode `logout()` ne
 * contient donc aucune logique applicative.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class SecurityController extends AbstractController
{
    /**
     * Affiche et traite le formulaire de connexion.
     *
     * Récupère l'éventuelle erreur d'authentification du dernier essai
     * ainsi que le dernier identifiant saisi pour pré-remplir le formulaire.
     * Cette approche améliore l'expérience utilisateur en cas d'erreur.
     *
     * @param AuthenticationUtils $authenticationUtils service Symfony fournissant
     *                                                 les données du dernier essai de connexion
     *
     * @return Response la vue Twig du formulaire de connexion
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupération de l'erreur d'authentification si elle existe (mauvais identifiants, etc.)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupération du dernier email saisi pour pré-remplissage du formulaire
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Point d'entrée de la déconnexion.
     *
     * Cette méthode ne contient aucune logique : elle est interceptée
     * et traitée directement par la configuration du pare-feu Symfony
     * (clé `logout` dans `security.yaml`) avant d'être exécutée.
     *
     * @throws \LogicException toujours levée si la méthode est appelée directement
     *                         (ne devrait jamais arriver en production)
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
