<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

class BasicCredentials implements CredentialsInterface
{
    private $username;
    private $password;
    private $host;

    public function __construct(string $username = '', string $password = '', string $host = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->host = strtolower($host);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function isEmpty(): bool
    {
        return empty($this->username);
    }

    public function getValue(): string
    {
        return base64_encode(sprintf(
            '%s:%s',
            $this->username,
            $this->password
        ));
    }
}
