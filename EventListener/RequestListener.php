<?php

namespace Vesax\HttpKernelLoggerBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Vesax\HttpKernelLoggerBundle\Logger\Formatter;

/**
 * Class TerminateListener
 *
 * @package Vesax\HttpKernelLoggerBundle
 * @author  Artur Vesker
 */
class RequestListener implements EventSubscriberInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Formatter
     */
    protected $formatter;

    /**
     * @var string
     */
    protected $rule;

    /**
     * TerminateListener constructor.
     *
     * @param LoggerInterface $logger
     * @param Formatter       $formatter
     * @param                 $rule
     */
    public function __construct(LoggerInterface $logger, Formatter $formatter, $rule)
    {
        $this->logger = $logger;
        $this->formatter = $formatter;
        $this->rule = $rule;
    }

    /**
     * @param GetResponseEvent $event ;
     */
    public function onRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->getRealMethod() == 'OPTIONS') {
            return;
        };

        if (!preg_match($this->rule, $request->getPathInfo())) {
            return;
        }

        $response = $event->getResponse();
        $message = $this->formatter->format($request, $response);

        $this->logger->info($message);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest'
        ];
    }

}
