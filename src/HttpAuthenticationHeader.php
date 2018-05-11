<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

use Psr\Http\Message\RequestInterface;

class HttpAuthenticationHeader
{
    const NAME = 'Authorization';

    /**
     * @var HttpAuthenticationCredentials
     */
    private $httpAuthenticationCredentials;

    /**
     * @param HttpAuthenticationCredentials $httpAuthenticationCredentials
     */
    public function __construct(HttpAuthenticationCredentials $httpAuthenticationCredentials)
    {
        $this->httpAuthenticationCredentials = $httpAuthenticationCredentials;
    }

    /**
     * @param RequestInterface $request
     *
     * @return bool
     */
    public function isValidForRequest(RequestInterface $request)
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

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $usernamePasswordPart = base64_encode(sprintf(
            '%s:%s',
            $this->httpAuthenticationCredentials->getUsername(),
            $this->httpAuthenticationCredentials->getPassword()
        ));

        return 'Basic ' . $usernamePasswordPart;
    }
}
