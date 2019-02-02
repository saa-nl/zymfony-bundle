<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestBridge
{
    /**
     * @param Request $symfonyRequest
     * @return \Zend_Controller_Request_Http
     * @throws \Zend_Controller_Request_Exception
     */
    public static function fromSymfonyRequest(Request $symfonyRequest): \Zend_Controller_Request_Http
    {
        $zendRequest = new \Zend_Controller_Request_Http();
        $zendRequest->setPathInfo();

        foreach ($symfonyRequest->attributes as $attribute => $value) {
            $zendRequest->setParam($attribute, $value);

            if ($attribute === $zendRequest->getModuleKey()) {
                $zendRequest->setModuleName($value);
            }

            if ($attribute === $zendRequest->getControllerKey()) {
                $zendRequest->setControllerName($value);
            }

            if ($attribute === $zendRequest->getActionKey()) {
                $zendRequest->setActionName($value);
            }
        }

        return $zendRequest;
    }

    public static function toSymfonyRequest(\Zend_Controller_Request_Http $zendRequest): Request
    {
        $symfonyRequest = Request::createFromGlobals();

        foreach ($zendRequest->getParams() as $param => $value) {
            $symfonyRequest->attributes->set($param, $value);
        }

        return $symfonyRequest;
    }
}
