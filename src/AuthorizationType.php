<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication;

class AuthorizationType
{
    const BASIC = 'Basic';
    const BEARER = 'Bearer';
    const DIGEST = 'Digest';
    const HOBA = 'HOBA';
    const MUTUAL = 'Mutual';
    const NEGOTIATE = 'Negotiate';
    const OAUTH = 'OAuth';
    const SCRAM_SHA_1 = 'SCRAM-SHA-1';
    const SCRAM_SHA_256 = 'SCRAM-SHA-256';
    const VAPID = 'vapid';
}
