<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Partner;
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
        $linesByPartner = $this->groupOrderLinesByPartner($order);

        if (empty($linesByPartner)) {
            $this->logger->warning('Aucune ligne de commande trouvée pour la commande ' . $order->getOrderNumber());
            return;
        }

        foreach ($linesByPartner as $partnerId => $lines) {
            $partner = $lines[0]['stock']->getPartner();

            if (!$partner) continue;

            // On récupère l'email du premier utilisateur lié au partenaire
            $partnerEmail = null;
            foreach ($partner->getUsers() as $user) {
                if ($user->getEmail()) {
                    $partnerEmail = $user->getEmail();
                    break; // On prend le premier trouvé
                }
            }

            if (!$partnerEmail) {
                $this->logger->error('Email introuvable pour le partenaire : ' . $partner->getCompanyName());
                continue;
            }

            try {
                $email = (new TemplatedEmail())
                    ->from(new Address('contact@pepiplus.fr', 'Pépi+'))
                    ->to($partnerEmail)
                    ->subject(sprintf('Nouvelle commande #%s - Pépi+', $order->getOrderNumber()))
                    ->htmlTemplate('emails/order_notification_partner.html.twig')
                    ->context([
                        'order' => $order,
                        'partner' => $partner,
                        'orderLines' => $lines,
                    ]);

                $this->mailer->send($email);
                $this->logger->info('Email envoyé avec succès à ' . $partnerEmail);
            } catch (\Exception $e) {
                $this->logger->error('Échec de l\'envoi au partenaire ' . $partnerId . ' : ' . $e->getMessage());
            }
        }
    }

    private function groupOrderLinesByPartner(Order $order): array
    {
        $grouped = [];
        // Utilisation de getOrderLines() qui doit renvoyer la collection
        foreach ($order->getOrderLines() as $line) {
            $stock = $line->getStock();
            if ($stock && $stock->getPartner()) {
                $partnerId = $stock->getPartner()->getId();
                $grouped[$partnerId][] = [
                    'plant' => $line->getStock()->getPlant(),
                    'quantity' => $line->getQuantity(),
                    'packaging' => $line->getStock()->getPackaging()?->getLabel(),
                    'season' => $line->getStock()->getSeason()?->getYear(),
                    'stock' => $stock
                ];
            }
        }
        return $grouped;
    }
}
