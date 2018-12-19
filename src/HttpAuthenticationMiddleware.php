<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

use Psr\Http\Message\RequestInterface;

class HttpAuthenticationMiddleware
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $credentials = null;

    /**
     * @var null string
     */
    private $host = null;

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function setCredentials(string $credentials)
    {
        $this->credentials = $credentials;
    }

    public function setHost(string $host)
    {
        $this->host = $host;
    }

    public function clearType()
    {
        $this->type = null;
    }

    public function clearCredentials()
    {
        $this->credentials = null;
    }

    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use (&$handler) {
            if (null === $this->type || null === $this->credentials || null === $this->host) {
                return $handler($request, $options);
            }

            $httpAuthenticationHeader = new AuthorizationHeader($this->type, $this->credentials);

            if (empty($this->credentials)) {
                return $handler($request, $options);
            }

            $requestHost = $request->getHeaderLine('host');

            $hasHostMatch = $requestHost === $this->host
                && preg_match('/' . preg_quote($this->host, '//') . '$/', $requestHost) > 0;

            if (!$hasHostMatch) {
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
