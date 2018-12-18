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
     * @var CredentialsInterface
     */
    private $credentials = null;

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function setCredentials(CredentialsInterface $credentials)
    {
        $this->credentials = $credentials;
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
            if (null === $this->type || null === $this->credentials) {
                return $handler($request, $options);
            }

            $httpAuthenticationHeader = new AuthorizationHeader($this->type, $this->credentials);

            if ($this->credentials->isEmpty()) {
                return $handler($request, $options);
            }

            $host = $request->getHeaderLine('host');
            $credentialsHost = $this->credentials->getHost();

            $hasHostMatch = $host === $credentialsHost
                && preg_match('/' . preg_quote($credentialsHost, '//') . '$/', $host) > 0;

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
