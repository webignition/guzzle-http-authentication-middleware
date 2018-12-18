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

    public function setIsSingleUse(bool $isSingleUse)
    {
        $this->isSingleUse = $isSingleUse;
    }

    public function setHttpAuthenticationCredentials(HttpAuthenticationCredentials $httpAuthenticationCredentials)
    {
        $this->httpAuthenticationCredentials = $httpAuthenticationCredentials;
    }

    public function __invoke(callable $handler): callable
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
