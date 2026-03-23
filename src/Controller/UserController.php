<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\SearchType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de gestion des utilisateurs (User).
 *
 * Réservé aux administrateurs pour la gestion complète des comptes :
 * création, modification, consultation et gestion des mots de passe.
 *
 * Deux mécanismes de modification de mot de passe sont disponibles pour l'admin :
 * - Réinitialisation forcée avec mot de passe provisoire `Password123!` + flag `mustChangePassword`
 * - Saisie manuelle d'un nouveau mot de passe directement
 *
 * Accessible à tous les utilisateurs connectés :
 * - Changement de son propre mot de passe (`/profile/change-password`)
 * - Modification de son profil personnel (`/my-account/profile`)
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/user')]
final class UserController extends AbstractController
{
    /**
     * Affiche la liste paginée des utilisateurs avec recherche.
     *
     * La recherche s'effectue sur l'email, le prénom, le nom,
     * les rôles et le statut actif/inactif.
     * Résultats paginés à 10 par page, triés par statut actif décroissant.
     *
     * @param Request            $request        requête HTTP (paramètre GET `query`)
     * @param UserRepository     $userRepository repository des utilisateurs
     * @param PaginatorInterface $paginator      service de pagination KnpPaginator
     *
     * @return Response la vue Twig avec la liste paginée et le formulaire de recherche
     */
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        // Récupération du terme de recherche depuis l'URL via le paramètre 'query'
        $searchTerm = $request->query->get('query');
        $allUsers = $userRepository->searchByTerm($searchTerm);

        $pagination = $paginator->paginate(
            $allUsers,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/index.html.twig', [
            'searchForm' => $form->createView(),
            'users' => $pagination,
        ]);
    }

    /**
     * Crée un nouvel utilisateur.
     *
     * Si un mot de passe est fourni dans le formulaire, il est haché avant
     * la persistance. L'utilisateur peut être associé à un partenaire si
     * le rôle PARTNER est sélectionné.
     *
     * @param Request                     $request        requête HTTP
     * @param EntityManagerInterface      $entityManager  gestionnaire d'entités Doctrine
     * @param UserPasswordHasherInterface $passwordHasher service de hachage des mots de passe
     *
     * @return Response la vue du formulaire ou redirection vers la liste
     */
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe s'il est fourni dans le formulaire
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }
            try {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Utilisateur ajouté avec succès !');

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Impossible d\'ajouter l\'utilisateur : '.$e->getMessage());
            }
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Affiche les détails d'un utilisateur.
     *
     * @param User $user L'utilisateur à afficher
     *
     * @return Response la vue Twig de détail (email, rôles, statut)
     */
    #[Route('/show/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Modifie un utilisateur (Admin uniquement).
     *
     * Deux cas de modification du mot de passe sont gérés :
     * - **Cas 1 — Réinitialisation forcée** : la case "Réinitialiser" est cochée.
     *   Le mot de passe devient `Password123!` et `mustChangePassword` est activé.
     *   L'utilisateur sera forcé à changer son mot de passe à sa prochaine connexion (RG-15).
     * - **Cas 2 — Saisie manuelle** : un nouveau mot de passe est saisi directement
     *   dans le champ prévu à cet effet.
     *
     * @param Request                     $request        requête HTTP
     * @param User                        $user           L'utilisateur à modifier
     * @param EntityManagerInterface      $entityManager  gestionnaire d'entités Doctrine
     * @param UserPasswordHasherInterface $passwordHasher service de hachage des mots de passe
     *
     * @return Response la vue du formulaire ou redirection vers la liste
     */
    #[Route('/edit/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Cas 1 : Réinitialisation forcée du mot de passe (RG-15)
            // Le flag mustChangePassword force le changement à la prochaine connexion
            if ($form->get('resetPassword')->getData()) {
                $user->setPassword($passwordHasher->hashPassword($user, 'Password123!'));
                $user->setMustChangePassword(true);
                $this->addFlash('warning', 'Le mot de passe a été réinitialisé à : Password123!');
            }

            // Cas 2 : Saisie manuelle d'un nouveau mot de passe par l'admin
            $plainPassword = $form->get('password')->getData();
            if (!empty($plainPassword)) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                $this->addFlash('success', 'Le mot de passe a été mis à jour manuellement.');
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Permet à l'utilisateur connecté de changer son propre mot de passe.
     *
     * Après modification réussie, le flag `mustChangePassword` est remis à `false`
     * pour libérer l'utilisateur d'une contrainte de changement forcé (RG-15).
     * Redirige vers le tableau de bord après succès.
     *
     * @param Request                     $request        requête HTTP
     * @param EntityManagerInterface      $entityManager  gestionnaire d'entités Doctrine
     * @param UserPasswordHasherInterface $passwordHasher service de hachage des mots de passe
     *
     * @return Response la vue du formulaire ou redirection vers le tableau de bord
     */
    #[Route('/profile/change-password', name: 'app_user_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Formulaire simplifié avec uniquement le champ plainPassword et sa confirmation
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            // Libération du flag mustChangePassword : l'utilisateur n'est plus contraint
            $user->setMustChangePassword(false);

            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été mis à jour.');

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('user/change_password.html.twig', ['form' => $form]);
    }

    /**
     * Affiche et permet la modification du profil de l'utilisateur connecté.
     *
     * Le formulaire est configuré en mode `is_profile = true` pour masquer
     * les champs réservés à l'administration (rôles, statut, réinitialisation).
     * L'email est affiché en lecture seule pour l'utilisateur (RG-16).
     *
     * @param Request                $request       requête HTTP
     * @param EntityManagerInterface $entityManager gestionnaire d'entités Doctrine
     *
     * @return Response la vue du profil utilisateur
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException si l'utilisateur n'est pas connecté
     */
    #[Route('/my-account/profile', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté.');
        }

        // Mode profil : champs d'administration masqués, email en lecture seule (RG-16)
        $form = $this->createForm(UserType::class, $user, [
            'is_profile' => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
