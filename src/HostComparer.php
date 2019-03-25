<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

class HostComparer
{
    public function isHostMatch(string $requestHost, string $authenticationHost)
    {
        $requestHost = strtolower($requestHost);
        $comparatorHost = strtolower($authenticationHost);

        return $requestHost === $comparatorHost
            || preg_match('*' . preg_quote($comparatorHost, '*') . '$*i', $requestHost) > 0;
    }
}
