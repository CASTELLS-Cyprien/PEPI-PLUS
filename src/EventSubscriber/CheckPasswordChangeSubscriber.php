<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CheckPasswordChangeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // On ne fait rien si ce n'est pas la requête principale (ex: fragments Twig)
        if (!$event->isMainRequest()) {
            return;
        }

        $routeName = $event->getRequest()->attributes->get('_route');

        // On évite la boucle infinie : ne pas rediriger si on est déjà sur la page de changement,
        // sur la page de login, ou si on essaie de se déconnecter.
        if (in_array($routeName, ['app_user_change_password', 'app_login', 'app_logout'])) {
            return;
        }

        /** @var User|null $user */
        $user = $this->security->getUser();

        // Si l'utilisateur est connecté et que le flag est à true
        if ($user && method_exists($user, 'isMustChangePassword') && $user->isMustChangePassword()) {
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