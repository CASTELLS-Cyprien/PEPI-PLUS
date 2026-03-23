<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur d'inscription des nouveaux utilisateurs.
 *
 * Gère la création d'un compte utilisateur via le formulaire d'inscription.
 * Le mot de passe en clair est haché avant la persistance en base de données.
 * Après inscription réussie, l'utilisateur est automatiquement connecté
 * via l'authentificateur personnalisé {@see AppCustomAuthenticator}.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class RegistrationController extends AbstractController
{
    /**
     * Affiche et traite le formulaire d'inscription.
     *
     * Processus d'inscription :
     * 1. Création d'un nouvel objet User.
     * 2. Récupération et hachage du mot de passe en clair.
     * 3. Persistance de l'utilisateur en base de données.
     * 4. Connexion automatique de l'utilisateur nouvellement créé.
     *
     * @param Request                     $request            requête HTTP
     * @param UserPasswordHasherInterface $userPasswordHasher service de hachage des mots de passe
     * @param Security                    $security           service Symfony pour la connexion automatique
     * @param EntityManagerInterface      $entityManager      gestionnaire d'entités Doctrine
     *
     * @return Response la vue du formulaire d'inscription ou connexion automatique après succès
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hachage du mot de passe en clair avant persistance (bcrypt via Symfony Security)
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // Connexion automatique de l'utilisateur après inscription réussie
            return $security->login($user, AppCustomAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
