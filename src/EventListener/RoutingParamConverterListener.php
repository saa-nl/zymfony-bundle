<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\EventListener;

use SAA\ZymfonyBundle\Routing\ZendControllerLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RoutingParamConverterListener implements EventSubscriberInterface
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        foreach ($request->attributes->all() as $param => $value) {
            if (substr($param, 0, 4) !== ZendControllerLoader::PARAM_KEY_VAL || $value === null) {
                continue;
            }

            $request->attributes->set(
                $request->attributes->get(ZendControllerLoader::PARAM_KEY_KEY . substr($param, 4)),
                $value
            );
        }

        foreach ($request->attributes->all() as $param => $value) {
            if (
                substr($param, 0, 4) !== ZendControllerLoader::PARAM_KEY_VAL &&
                substr($param, 0, 4) !== ZendControllerLoader::PARAM_KEY_KEY) {
                continue;
            }

            $request->attributes->remove($param);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
