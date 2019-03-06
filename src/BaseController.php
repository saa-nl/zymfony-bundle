<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle;

use SAA\ZymfonyBundle\Routing\ZendControllerLoader;
use Symfony\Component\HttpFoundation\Response;

class BaseController extends \Zend_Controller_Action
{
    /**
     * @var ControllerActionCallerInterface
     */
    private $caller;

    /**
     * @var RendererInterface
     */
    private $renderer;

    public function setCaller(ControllerActionCallerInterface $caller)
    {
        $this->caller = $caller;
    }

    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param string $methodName
     * @param array $args
     * @return Response|null
     * @throws \Zend_Controller_Action_Exception
     */
    public function __call($methodName, $args)
    {
        $prefixLength = strlen(ZendControllerLoader::ROUTE_PREFIX);
        if (substr($methodName, 0, $prefixLength) !== ZendControllerLoader::ROUTE_PREFIX) {
            parent::__call($methodName, $args);
            return null;
        }

        $methodName = str_replace(ZendControllerLoader::ROUTE_PREFIX, '', $methodName);
        $controllerResponse = $this->caller->call($this, $methodName, $args);
        if ($controllerResponse instanceof Response) {
            return $controllerResponse;
        }

        return $this->renderer->render($this->view, $this->_helper);
    }
}
