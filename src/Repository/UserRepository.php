<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Repository des utilisateurs (User).
 *
 * Implémente {@see PasswordUpgraderInterface} pour le re-hachage automatique
 * des mots de passe par Symfony Security lorsque l'algorithme de hachage évolue.
 *
 * Fournit une méthode de recherche multi-champs utilisée dans la liste
 * des utilisateurs de {@see UserController::index()}.
 *
 * @extends ServiceEntityRepository<User>
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Initialise le repository avec le registre Doctrine.
     *
     * @param ManagerRegistry $registry registre Doctrine
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Met à jour automatiquement le hash du mot de passe d'un utilisateur.
     *
     * Appelé automatiquement par Symfony Security lors de la connexion si
     * l'algorithme de hachage configuré a changé depuis le dernier hash.
     * Permet une migration transparente vers de nouveaux algorithmes sans
     * imposer un changement de mot de passe aux utilisateurs.
     *
     * @param PasswordAuthenticatedUserInterface $user              L'utilisateur dont le mot de passe doit être mis à jour
     * @param string                             $newHashedPassword nouveau mot de passe haché avec l'algorithme actuel
     *
     * @throws UnsupportedUserException si l'utilisateur n'est pas une instance de {@see User}
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Recherche des utilisateurs par email, prénom, nom, rôles ou statut actif.
     *
     * La recherche s'effectue simultanément sur cinq champs via des conditions OR.
     * La recherche sur `roles` (JSON) permet de retrouver les utilisateurs par leur
     * rôle applicatif (ex : rechercher "ADMIN" retourne les administrateurs).
     * Résultats triés par statut actif décroissant (actifs en premier).
     *
     * @param string|null $term terme de recherche (peut être null pour tout retourner)
     *
     * @return User[] tableau des utilisateurs triés par statut actif décroissant
     */
    public function searchByTerm(?string $term): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($term) {
            // Recherche multi-champs : email, prénom, nom, rôles (JSON) et statut
            $qb->andWhere('p.email LIKE :term OR p.firstName LIKE :term OR p.lastName LIKE :term OR p.roles LIKE :term OR p.isActive LIKE :term')
                ->setParameter('term', '%'.$term.'%');
        }

        // Tri par statut actif décroissant : les comptes actifs apparaissent en premier
        return $qb->orderBy('p.isActive', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
