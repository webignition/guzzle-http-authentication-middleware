<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

class HostComparer
{
    public function isHostMatch(string $requestHost, string $authenticationHost)
    {
        $requestHost = strtolower($requestHost);
        $authenticationHost = strtolower($authenticationHost);

        return $requestHost === $authenticationHost
            || preg_match('*' . preg_quote($authenticationHost, '*') . '$*i', $requestHost) > 0;
    }
}
