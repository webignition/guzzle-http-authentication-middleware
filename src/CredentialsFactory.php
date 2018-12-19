<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

class CredentialsFactory
{
    public static function createBasicCredentials(string $username, string $password): string
    {
        return base64_encode(sprintf(
            '%s:%s',
            $username,
            $password
        ));
    }
}
