<?php

namespace App\EventListener;

use App\Service\CacheService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class ResponseListener implements EventSubscriberInterface
{
    public function __construct(private readonly CacheService $cacheService)
    {

    }

    /**
     * @param  ResponseEvent $event
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $responseCache = (int)$event->getRequest()->headers->get('Response-Cache') ?? 0;

        if ($responseCache) {
            // Set the query string params
            $requestContent = $event->getRequest()->getQueryString();
            if (!empty($requestContent)) {
                // Save the response content into the cache
                // Next same request will be provided from cache in src/EventListener/RequestListener.php
                $requestHash = md5($requestContent);
                $this->cacheService->set($requestHash, $event->getResponse()->getContent(), $_ENV['RESPONSE_CACHE_TIMEOUT']);
                $event->getResponse()->headers->set('Response-Cache-Timeout', $_ENV['RESPONSE_CACHE_TIMEOUT']);
            }
        }
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}