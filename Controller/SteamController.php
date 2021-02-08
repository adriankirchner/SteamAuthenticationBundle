<?php

namespace Knojector\SteamAuthenticationBundle\Controller;

use Knojector\SteamAuthenticationBundle\DTO\SteamCallback;
use Knojector\SteamAuthenticationBundle\Event\CallbackReceivedEvent;
use Knojector\SteamAuthenticationBundle\Exception\SteamAuthenticationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author knojector <dev@knojector.xyz>
 *
 * @Route("/steam")
 */
class SteamController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route("/callback")
     */
    public function callback(SteamCallback $callback): Response
    {
        try {
            $this->eventDispatcher->dispatch(new CallbackReceivedEvent($callback), CallbackReceivedEvent::NAME);
        } catch (SteamAuthenticationException $e) {
            return new RedirectResponse(
                $this->urlGenerator->generate($this->getParameter('knojector.steam_authentication.login_failure_redirect'))
            );
        }

        return new RedirectResponse(
            $this->urlGenerator->generate($this->getParameter('knojector.steam_authentication.login_success_redirect'))
        );
    }
}