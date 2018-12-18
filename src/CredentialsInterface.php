<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

interface CredentialsInterface
{
    public function isEmpty(): bool;
    public function getValue(): string;
    public function getHost(): string;
}
