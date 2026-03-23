<?php

namespace App\Service;

use App\Entity\Order;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Service d'envoi des notifications email aux partenaires lors d'une commande.
 *
 * À la validation d'une commande, ce service identifie les partenaires
 * dont les stocks ont été réservés et leur envoie un email récapitulatif
 * avec les détails des plants commandés.
 *
 * Comportement :
 * - Groupe les lignes de commande par partenaire (un email par entreprise).
 * - Collecte tous les emails des utilisateurs rattachés à chaque partenaire.
 * - Envoie un email via le template Twig `emails/order_notification_partner.html.twig`.
 * - Logue les avertissements et erreurs sans bloquer le processus global.
 * - Un délai de 5 secondes est appliqué entre chaque email (anti-spam SMTP).
 *
 * Ce service est désactivé en v1 dans {@see \App\Controller\CartController::validate()} :
 * l'appel est commenté et peut être réactivé en décommentant le bloc try/catch.
 *
 * @author CASTELLS Cyprien
 *
 * @version 1.2
 */
class OrderNotificationService
{
    /**
     * Initialise le service avec les dépendances d'envoi d'emails et de logging.
     *
     * @param MailerInterface $mailer service d'envoi d'emails Symfony Mailer
     * @param LoggerInterface $logger service de journalisation PSR-3
     */
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Notifie par email les partenaires concernés par une commande.
     *
     * Processus d'envoi :
     * 1. **Groupement** : parcourt toutes les lignes de commande et regroupe
     *    celles appartenant au même partenaire en un seul groupe.
     *    Les lignes sans partenaire (stock interne) sont ignorées avec un warning.
     * 2. **Collecte des destinataires** : récupère tous les emails des utilisateurs
     *    rattachés à chaque partenaire.
     * 3. **Envoi** : envoie un email récapitulatif par partenaire avec un délai
     *    de 5 secondes entre chaque envoi (protection anti-spam SMTP).
     *    Les erreurs d'envoi sont loguées sans bloquer les envois suivants.
     *
     * @param Order $order la commande validée dont les partenaires doivent être notifiés
     */
    public function notifyPartnersForOrder(Order $order): void
    {
        $linesByPartner = [];

        // Étape 1 : Groupement des lignes par partenaire
        foreach ($order->getOrderLines() as $line) {
            $stock = $line->getStock();

            // Vérification de la chaîne complète stock → partenaire
            if (!$stock || !$stock->getPartner()) {
                $this->logger->warning('Ligne de commande sans partenaire : ID '.$line->getId());
                continue;
            }

            $partner = $stock->getPartner();
            $partnerId = $partner->getId();

            if (!isset($linesByPartner[$partnerId])) {
                $linesByPartner[$partnerId] = [
                    'partner' => $partner,
                    'lines' => [],
                ];
            }
            $linesByPartner[$partnerId]['lines'][] = $line;
        }

        // Log du nombre de partenaires distincts identifiés dans la commande
        $this->logger->info(sprintf(
            'Commande %s : %d entreprise(s) distincte(s) identifiée(s).',
            $order->getOrderNumber(),
            count($linesByPartner)
        ));

        // Étape 2 : Envoi d'un email par partenaire
        foreach ($linesByPartner as $partnerId => $data) {
            $partner = $data['partner'];
            $lines = $data['lines'];

            // Collecte de tous les emails des utilisateurs du partenaire
            $recipients = [];
            foreach ($partner->getUsers() as $user) {
                if ($user->getEmail()) {
                    $recipients[] = $user->getEmail();
                }
            }

            if (empty($recipients)) {
                $this->logger->error('Aucun email configuré pour le partenaire : '.$partner->getCompanyName());
                continue;
            }

            try {
                $email = (new TemplatedEmail())
                    ->from(new Address('contact@pepiplus.fr', 'Pépi+'))
                    // Envoi à tous les utilisateurs du partenaire en un seul email
                    ->to(...$recipients)
                    ->subject('Nouvelle commande #'.$order->getOrderNumber())
                    ->htmlTemplate('emails/order_notification_partner.html.twig')
                    ->context([
                        'order' => $order,
                        'partner' => $partner,
                        'orderLines' => $lines,
                    ]);

                $this->mailer->send($email);
                $this->logger->info('Email de notification envoyé à : '.$partner->getCompanyName());

                // Délai anti-spam entre les envois successifs
                sleep(5);
            } catch (\Exception $e) {
                // L'erreur est loguée mais ne bloque pas les notifications aux autres partenaires
                $this->logger->error("Erreur envoi email partenaire ID $partnerId : ".$e->getMessage());
            }
        }
    }
}
