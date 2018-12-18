<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

use Psr\Http\Message\RequestInterface;

class HttpAuthenticationHeader
{
    const NAME = 'Authorization';

    private $httpAuthenticationCredentials;

    public function __construct(HttpAuthenticationCredentials $httpAuthenticationCredentials)
    {
        $this->httpAuthenticationCredentials = $httpAuthenticationCredentials;
    }

    public function isValidForRequest(RequestInterface $request): bool
    {
        if ($this->httpAuthenticationCredentials->isEmpty()) {
            return false;
        }

        $requestHost = $request->getHeaderLine('host');
        $domain = $this->httpAuthenticationCredentials->getDomain();

        if ($requestHost === $domain) {
            return true;
        }

        return preg_match('/' . preg_quote($domain, '//') . '$/', $requestHost) > 0;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getValue(): string
    {
        $usernamePasswordPart = base64_encode(sprintf(
            '%s:%s',
            $this->httpAuthenticationCredentials->getUsername(),
            $this->httpAuthenticationCredentials->getPassword()
        ));

        return 'Basic ' . $usernamePasswordPart;
    }
}
