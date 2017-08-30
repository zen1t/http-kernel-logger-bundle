<?php

namespace Vesax\HttpKernelLoggerBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Vesax\HttpKernelLoggerBundle\Logger\Formatter;

/**
 * Class TerminateListener
 *
 * @package Vesax\HttpKernelLoggerBundle
 * @author  Artur Vesker
 */
class ResponseListener implements EventSubscriberInterface
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
     * @param FilterResponseEvent $event ;
     */
    public function onResponse(FilterResponseEvent $event)
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
        if ($response->isClientError()) {
            $this->logger->error($message);

            return;
        }

        if ($response->isServerError()) {
            $this->logger->critical($message);

            return;
        }

        $this->logger->info($message);
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onResponse'
        ];
    }

}
