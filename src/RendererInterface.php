<?php declare(strict_types=1);

namespace SAA\ZymfonyBundle;

use Symfony\Component\HttpFoundation\Response;

interface RendererInterface
{
    /**
     * Render the zend view to a Symfony response.
     * You can implement this to render through Zend, Twig or any other rendering engine.
     *
     * @param \Zend_View_Interface $view
     * @param \Zend_Controller_Action_HelperBroker $helper
     * @return Response
     */
    public function render($view, $helper): Response;
}
