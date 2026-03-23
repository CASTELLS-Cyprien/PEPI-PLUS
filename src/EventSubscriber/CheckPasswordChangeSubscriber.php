<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CheckPasswordChangeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $routeName = $event->getRequest()->attributes->get('_route');

        if (in_array($routeName, ['app_user_change_password', 'app_login', 'app_logout'])) {
            return;
        }

        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            $this->security->logout(false); // invalide la session
            $response = new RedirectResponse($this->urlGenerator->generate('app_login'));
            $event->setResponse($response);

            return;
        }

        // Existant : forcer le changement de mot de passe
        if ($user->isMustChangePassword()) {
            $response = new RedirectResponse($this->urlGenerator->generate('app_user_change_password'));
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
