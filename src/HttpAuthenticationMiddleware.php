<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

use Psr\Http\Message\RequestInterface;

class HttpAuthenticationMiddleware
{
    /**
     * @var HttpAuthenticationCredentials
     */
    private $httpAuthenticationCredentials;

    /**
     * @var bool
     */
    private $isSingleUse = false;

    public function __construct()
    {
        $this->httpAuthenticationCredentials = new HttpAuthenticationCredentials();
    }

    /**
     * @param $isSingleUse
     */
    public function setIsSingleUse($isSingleUse)
    {
        $this->isSingleUse = $isSingleUse;
    }

    /**
     * @param HttpAuthenticationCredentials $httpAuthenticationCredentials
     */
    public function setHttpAuthenticationCredentials(HttpAuthenticationCredentials $httpAuthenticationCredentials)
    {
        $this->httpAuthenticationCredentials = $httpAuthenticationCredentials;
    }

    /**
     * @param callable $handler
     *
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use (&$handler) {
            $httpAuthenticationHeader = new HttpAuthenticationHeader($this->httpAuthenticationCredentials);

            if ($this->isSingleUse) {
                $this->httpAuthenticationCredentials = new HttpAuthenticationCredentials();
                $this->isSingleUse = false;
            }

            if (!$httpAuthenticationHeader->isValidForRequest($request)) {
                return $handler($request, $options);
            }

            return $handler(
                $request->withHeader(
                    $httpAuthenticationHeader->getName(),
                    $httpAuthenticationHeader->getValue()
                ),
                $options
            );
        };
    }
}
