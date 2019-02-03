<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\EventListener;

use SAA\ZymfonyBundle\DependencyInjection\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RegistryListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseEvent $event
     * @throws \Zend_Exception
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $zendRegistryInstance = \Zend_Registry::getInstance();

        $zymfonyRegistryInstance = new Registry($zendRegistryInstance->getArrayCopy());
        $zymfonyRegistryInstance->setContainer($this->container);

        \Zend_Registry::_unsetInstance();
        \Zend_Registry::setInstance($zymfonyRegistryInstance);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest'
        ];
    }
}
