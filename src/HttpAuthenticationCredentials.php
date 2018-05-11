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
    public function __construct($username = '', $password = '', $domain = '')
    {
        $this->username = $username;
        $this->password = $password;
        $this->domain = strtolower($domain);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->username);
    }
}
