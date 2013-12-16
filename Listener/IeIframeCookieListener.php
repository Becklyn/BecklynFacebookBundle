<?php

namespace Becklyn\FacebookBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Handles the P3P headers for internet explorer
 */
class IeIframeCookieListener
{
    /**
     * Adds the necessary headers for IE to handle session and cookies in an iframe correctly
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse (FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $response->headers->set("P3P", 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
    }
}