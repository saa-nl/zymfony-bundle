<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\EventListener;

use SAA\ZymfonyBundle\Routing\ZendControllerLoader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RoutingParamConverterListener implements EventSubscriberInterface
{
    private const ZEND_ATTRIBUTE_MODULE_KEY = 'module';
    private const ZEND_ATTRIBUTE_ACTION_KEY = 'action';

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

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        $request->attributes->set(self::ZEND_ATTRIBUTE_ACTION_KEY, strtolower(\Zend_Filter::filterStatic(
            str_replace(
                'Action',
                '',
                explode('::', $request->attributes->get('_controller'))[1]
            ),
            'Word_CamelCaseToDash'
        )));

        if (strstr((string)$request->attributes->get('_route'), 'zend_custom') === false) {
            // Custom routes in ZF1 did not have module default key
            $request->attributes->set(self::ZEND_ATTRIBUTE_MODULE_KEY, 'default');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }
}
