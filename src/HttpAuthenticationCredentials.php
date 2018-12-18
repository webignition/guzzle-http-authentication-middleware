<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

class HttpAuthenticationCredentials
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $domain;

    /**
     * @param string $username
     * @param string $password
     * @param string $domain
     */
    public function __construct(string $username = '', string $password = '', string $domain = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->domain = strtolower($domain);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function isEmpty(): bool
    {
        return empty($this->username);
    }
}
