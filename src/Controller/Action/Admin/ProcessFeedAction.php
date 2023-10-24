<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Admin;

use Symfony\Component\HttpFoundation\RequestStack;
use Setono\SyliusFeedPlugin\Message\Command\ProcessFeed;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProcessFeedAction
{
    private MessageBusInterface $commandBus;

    private UrlGeneratorInterface $urlGenerator;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(
        MessageBusInterface $commandBus,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        RequestStack $requestStack,
    ) {

        // Get the current request from the request stack
        $request = $requestStack->getCurrentRequest();

        $this->commandBus = $commandBus;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $request->getSession()->getBag('flashes');
        $this->translator = $translator;
    }

    public function __invoke(int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new ProcessFeed($id));
        $this->flashBag->add('success', $this->translator->trans('setono_sylius_feed.feed_generation_triggered'));

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_feed_admin_feed_show', ['id' => $id]));
    }
}
