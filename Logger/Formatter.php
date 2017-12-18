<?php

namespace Vesax\HttpKernelLoggerBundle\Logger;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Formatter
 *
 * @author Artur Vesker
 */
class Formatter
{

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return string
     */
    public function format(Request $request, Response $response = null)
    {
        if (null === $response) {
            return sprintf("%s %s      %s      %s",
                $request->getRealMethod(),
                $request->getRequestUri(),
                $this->formatHeaders($request->headers),
                $this->getContent($request)
            );
        }

        return sprintf("%s %s      %s      %s <<<< %s %s      %s      %s",
            $request->getRealMethod(),
            $request->getRequestUri(),
            $this->formatHeaders($request->headers),
            $this->getContent($request),

            $response->getStatusCode(),
            Response::$statusTexts[$response->getStatusCode()],
            $this->formatHeaders($response->headers),
            $response->getContent()
        );
    }

    private function formatHeaders(HeaderBag $headerBag)
    {
        $headers = $headerBag->all();

        $content = '';
        foreach ($headers as $name => $values) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            foreach ($values as $value) {
                $content .= sprintf("%s %s; ", $name.':', $value);
            }
        }

        return $content;
    }

    /**
     * @param Request $request
     *
     * @return null|resource|string
     */
    private function getContent(Request $request)
    {
        try {
            return $request->getContent();
        } catch (\Exception $e) {
            return null;
        }
    }

}
