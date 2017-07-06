<?php

namespace AppBundle\Listener;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

//use Symfony\Component\HttpKernel\HttpKernel;
//use Symfony\Component\HttpKernel\HttpKernelInterface;


class MaintenanceListener {

    private $templating;
    private $active;

    public function __construct(EngineInterface $templating, $active) {
        $this->templating = $templating;
        $this->active = $active;
    }

    private function maintenance(Response $response) {
        $html = $this->templating->render('maintenance/index.html.twig');
        $response->setContent($html);
        return $response;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }
        if ($this->active) {

            $response = $this->maintenance(new Response);
            $event->setResponse($response);
        }
    }

}
