<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\EventListener;

use SAA\ZymfonyBundle\Request\RequestBridge;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestConverterListener implements EventSubscriberInterface
{
    /**
     * @param FilterControllerEvent $event
     * @throws \Zend_Controller_Request_Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        /** @var \Zend_Controller_Action $controller */
        $controller = $event->getController()[0];
        if ($controller instanceof \Zend_Controller_Action === false) {
            return;
        }

        $controller->setRequest(RequestBridge::fromSymfonyRequest($event->getRequest()));
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }
}
