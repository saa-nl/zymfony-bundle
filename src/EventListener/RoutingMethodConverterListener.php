<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\EventListener;

use SAA\ZymfonyBundle\Routing\ZendControllerLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RoutingMethodConverterListener implements EventSubscriberInterface
{
    public function onKernelControllerArguments(FilterControllerArgumentsEvent $event)
    {
        $controller = $event->getController();
        if ($controller[0] instanceof \Zend_Controller_Action === false) {
            return;
        }

        $event->setController([
            $controller[0],
            ZendControllerLoader::ROUTE_PREFIX . $controller[1]
        ]);
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments'];
    }
}
