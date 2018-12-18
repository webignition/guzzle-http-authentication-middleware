<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

class AuthorizationHeader
{
    const NAME = 'Authorization';

    private $type;
    private $credentials;

    public function __construct(string $type, CredentialsInterface $credentials)
    {
        $this->type = $type;
        $this->credentials = $credentials;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getValue(): string
    {
        return sprintf(
            '%s %s',
            $this->type,
            $this->credentials->getValue()
        );
    }
}
