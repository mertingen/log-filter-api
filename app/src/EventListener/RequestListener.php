<?php

namespace App\EventListener;

use App\Service\CacheService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
class RequestListener implements EventSubscriberInterface
{
    public function __construct(private readonly CacheService $cacheService)
    {

    }

    /**
     * @param  RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // Set the query string params
        $requestContent = $event->getRequest()->getQueryString();

        $responseCache = (int)$event->getRequest()->headers->get('Response-Cache') ?? 0;

        if (!empty($requestContent) &&  !empty($responseCache)) {
            // Hash the request content and fetch it from cache if data are exist
            $requestHash = md5($requestContent);
            $content = $this->cacheService->get($requestHash);
            // Read data from cache and respond it to client
            if (!empty($content)) {
                $content = json_decode($content);
                $response = new JsonResponse($content);
                $response->headers->set('Response-Cache-Timeout', $_ENV['RESPONSE_CACHE_TIMEOUT']);
                $event->setResponse($response);
                return;
            }
        }

    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}