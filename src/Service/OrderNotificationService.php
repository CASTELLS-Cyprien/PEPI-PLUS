<?php

namespace App\Service;

use App\Entity\Order;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class OrderNotificationService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {}

    public function notifyPartnersForOrder(Order $order): void
    {
        $linesByPartner = [];

        // 1. Groupement : On s'assure de ne rien rater
        foreach ($order->getOrderLines() as $line) {
            $stock = $line->getStock();

            // Sécurité : on vérifie toute la chaîne
            if (!$stock || !$stock->getPartner()) {
                $this->logger->warning("Ligne de commande sans partenaire : ID " . $line->getId());
                continue;
            }

            $partner = $stock->getPartner();
            $partnerId = $partner->getId();

            if (!isset($linesByPartner[$partnerId])) {
                $linesByPartner[$partnerId] = [
                    'partner' => $partner,
                    'lines' => []
                ];
            }
            $linesByPartner[$partnerId]['lines'][] = $line;
        }

        // DEBUG : On logue combien d'entreprises ont été trouvées dans la commande
        $this->logger->info(sprintf(
            "Commande %s : %d entreprise(s) distincte(s) identifiée(s).",
            $order->getOrderNumber(),
            count($linesByPartner)
        ));

        // 2. Envoi des emails
        foreach ($linesByPartner as $partnerId => $data) {
            $partner = $data['partner'];
            $lines = $data['lines']; // Ici, il y aura vos 2 plants pour la Company A

            // Collecte de TOUS les emails des utilisateurs du partenaire
            $recipients = [];
            foreach ($partner->getUsers() as $user) {
                if ($user->getEmail()) {
                    $recipients[] = $user->getEmail();
                }
            }

            if (empty($recipients)) {
                $this->logger->error("Aucun email pour le partenaire : " . $partner->getCompanyName());
                continue; // On passe au partenaire suivant sans bloquer le reste
            }

            try {
                $email = (new TemplatedEmail())
                    ->from(new Address('contact@pepiplus.fr', 'Pépi+'))
                    ->to(...$recipients) // Envoie à tous les utilisateurs du partenaire
                    ->subject("Nouvelle commande #" . $order->getOrderNumber())
                    ->htmlTemplate('emails/order_notification_partner.html.twig')
                    ->context([
                        'order' => $order,
                        'partner' => $partner,
                        'orderLines' => $lines,
                    ]);

                $this->mailer->send($email);
                $this->logger->info("Email envoyé à " . $partner->getCompanyName());

                sleep(5);
            } catch (\Exception $e) {
                $this->logger->error("Erreur partenaire ID $partnerId : " . $e->getMessage());
            }
        }
    }
}
