<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle;

use Symfony\Component\HttpFoundation\Response;

interface ControllerActionCallerInterface
{
    /**
     * Call the controller action. You can use this if you have any additional DI on your Zend Controllers (like PHPDI)
     * If a response is returned, the response will be returned immediately to Symfony.
     * Otherwise, the renderer will be called.
     *
     * @param \Zend_Controller_Action $controller
     * @param string $action
     * @param array $arguments
     * @return Response|null
     */
    public function call(\Zend_Controller_Action $controller, string $action, array $arguments): ?Response;
}
