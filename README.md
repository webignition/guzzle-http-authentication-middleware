# Guzzle HTTP Authentication Middleware

## Overview

[Middleware](http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html#middleware) for [Guzzle 6](http://docs.guzzlephp.org/en/stable/) for setting [basic http authentication](https://en.wikipedia.org/wiki/Basic_access_authentication) on all requests sent by a client.

An authentication header is added to any valid request. A valid request is one where the request host matches a pre-specified domain name.

Useful if your circumstances match all or some of the following:

- you need to set HTTP authentication on all requests sent by a client for a specific domain only
- you don't want to specifically add an authorization header to each request made, particularly if there are many points across an application where requests are made
- you cannot determine in advance to which domains requests might be made and you don't want to leak credentials by means of setting an authorization header on every single request that your client sends

Maybe, just maybe, this is for you.

## Usage example

```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use webignition\Guzzle\Middleware\HttpAuthentication\AuthorizationType;
use webignition\Guzzle\Middleware\HttpAuthentication\BasicCredentials;
use webignition\Guzzle\Middleware\HttpAuthentication\AuthorizationHeader;
use webignition\Guzzle\Middleware\HttpAuthentication\HttpAuthenticationMiddleware;

// Creating a client that uses the middleware
$httpAuthenticationMiddleware = new HttpAuthenticationMiddleware();

$handlerStack = HandlerStack::create();
$handlerStack->push($httpAuthenticationMiddleware, 'http-auth');

$client = new Client([
    'handler' => $handlerStack,
]);

// Setting credentials on the middleware
$credentials = new BasicCredentials('username', 'password', 'example.com');
$httpAuthenticationMiddleware->setType(AuthorizationType::BASIC);
$httpAuthenticationMiddleware->setCredentials($credentials);

// All requests to example.com (or *.example.com) will now have
// a correct Authorization header set for basic HTTP authentication
```
## Application-level considerations

Let's assume you are building a modern PHP application that utilises controllers, services and so on.

Define your `HttpAuthenticationMiddleware` instance as a *service*. Use dependency injection to inject that service into whichever part of your application needs to set HTTP authentication credentials. Call `HttpAuthenticationMiddleware::setHttpAuthenticationCredentials()` as needed, passing in a `HttpAuthenticationCredentials` instance containing relevant values.

