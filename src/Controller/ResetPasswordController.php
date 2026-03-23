<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * Contrôleur de réinitialisation du mot de passe par email.
 *
 * Implémente le flux complet de réinitialisation sécurisée via le bundle
 * SymfonyCasts ResetPassword :
 * 1. L'utilisateur soumet son email → un token à usage unique est généré et envoyé.
 * 2. L'utilisateur clique sur le lien reçu → le token est stocké en session.
 * 3. L'utilisateur saisit son nouveau mot de passe → le token est validé et supprimé.
 *
 * Sécurité :
 * - Le token est à usage unique et expire après 24 heures.
 * - L'application ne révèle pas si un email est enregistré ou non.
 * - Le token est stocké en session (et supprimé de l'URL) pour éviter les fuites.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    /**
     * Initialise le contrôleur avec les dépendances nécessaires.
     *
     * @param ResetPasswordHelperInterface $resetPasswordHelper service de gestion des tokens de réinitialisation
     * @param EntityManagerInterface       $entityManager       gestionnaire d'entités Doctrine
     */
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Affiche et traite le formulaire de demande de réinitialisation de mot de passe.
     *
     * Si l'email soumis correspond à un compte existant, un email avec un lien
     * sécurisé est envoyé. Dans tous les cas, l'utilisateur est redirigé vers
     * la page de confirmation (pour ne pas révéler si l'email existe en base).
     *
     * @param Request             $request    requête HTTP
     * @param MailerInterface     $mailer     service d'envoi d'emails Symfony
     * @param TranslatorInterface $translator service de traduction pour les messages d'erreur
     *
     * @return Response la vue du formulaire de demande
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $form->get('email')->getData();

            return $this->processSendingPasswordResetEmail($email, $mailer, $translator);
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /**
     * Page de confirmation affichée après la demande de réinitialisation.
     *
     * Affiche le délai d'expiration du token envoyé par email.
     * Si l'utilisateur arrive directement sur cette page (sans token en session),
     * un faux token est généré pour ne pas révéler d'informations.
     *
     * @return Response la vue de confirmation avec les informations d'expiration
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Génération d'un faux token si l'utilisateur arrive directement sur cette page
        // (évite de révéler si un compte existe pour l'email saisi)
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Valide le token de réinitialisation et permet la saisie du nouveau mot de passe.
     *
     * Processus de validation :
     * 1. Si un token est présent dans l'URL, il est stocké en session et supprimé de l'URL
     *    (pour éviter les fuites vers des scripts tiers).
     * 2. Le token est récupéré depuis la session et validé.
     * 3. Si valide, l'utilisateur peut saisir son nouveau mot de passe.
     * 4. Après changement, le token est supprimé et la session nettoyée.
     *
     * @param Request                     $request        requête HTTP
     * @param UserPasswordHasherInterface $passwordHasher service de hachage des mots de passe
     * @param TranslatorInterface         $translator     service de traduction
     * @param string|null                 $token          token de réinitialisation (issu de l'URL)
     *
     * @return Response la vue du formulaire de nouveau mot de passe ou redirection
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        TranslatorInterface $translator,
        ?string $token = null,
    ): Response {
        if ($token) {
            // Stockage du token en session et suppression de l'URL pour éviter les fuites
            // vers des scripts JavaScript tiers qui pourraient intercepter l'URL
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            // Affichage de l'erreur de validation du token (expiré, invalide, déjà utilisé)
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Token valide : affichage du formulaire de nouveau mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Suppression du token après usage (usage unique)
            $this->resetPasswordHelper->removeResetRequest($token);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Hachage et mise à jour du nouveau mot de passe
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $this->entityManager->flush();

            // Nettoyage de la session après changement réussi
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    /**
     * Traite l'envoi de l'email de réinitialisation de mot de passe.
     *
     * Recherche l'utilisateur par son email. Si introuvable, redirige quand même
     * vers la page de confirmation pour ne pas révéler si le compte existe.
     * Génère un token sécurisé, l'envoie par email et le stocke en session.
     *
     * @param string              $emailFormData adresse email soumise par l'utilisateur
     * @param MailerInterface     $mailer        service d'envoi d'emails
     * @param TranslatorInterface $translator    service de traduction
     *
     * @return RedirectResponse redirection vers la page de confirmation d'envoi
     */
    private function processSendingPasswordResetEmail(
        string $emailFormData,
        MailerInterface $mailer,
        TranslatorInterface $translator,
    ): RedirectResponse {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Ne pas révéler si un compte existe pour cet email (sécurité anti-énumération)
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // En cas d'erreur de génération, redirection silencieuse vers la confirmation
            // (décommenter les lignes ci-dessous pour afficher le détail de l'erreur)
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
            //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            // ));

            return $this->redirectToRoute('app_check_email');
        }

        // Construction et envoi de l'email de réinitialisation via le template Twig
        $email = (new TemplatedEmail())
            ->from(new Address('contact@pepiplus.fr', 'Pépi+ Security'))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);

        $mailer->send($email);

        // Stockage du token en session pour récupération sur la page check-email
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');
    }
}
