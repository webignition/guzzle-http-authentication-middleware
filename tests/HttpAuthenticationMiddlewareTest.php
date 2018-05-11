<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication\Tests;

use Psr\Http\Message\RequestInterface;
use webignition\Guzzle\Middleware\HttpAuthentication\HttpAuthenticationCredentials;
use webignition\Guzzle\Middleware\HttpAuthentication\HttpAuthenticationHeader;
use webignition\Guzzle\Middleware\HttpAuthentication\HttpAuthenticationMiddleware;

class HttpAuthenticationMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpAuthenticationMiddleware
     */
    private $httpAuthenticationMiddleware;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->httpAuthenticationMiddleware = new HttpAuthenticationMiddleware();
    }

    public function testInvokeEmptyCredentials()
    {
        $request = \Mockery::mock(RequestInterface::class);
        $options = [];

        $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
            function ($returnedRequest, $returnedOptions) use ($request, $options) {
                $this->assertEquals($request, $returnedRequest);
                $this->assertEquals($options, $returnedOptions);
            }
        );

        $returnedFunction($request, $options);
    }

    public function testInvokeCredentialsInvalidForRequest()
    {
        $request = \Mockery::mock(RequestInterface::class);

        $request
            ->shouldReceive('getHeaderLine')
            ->with('host')
            ->andReturn('example.com');
        $options = [];

        $credentials = new HttpAuthenticationCredentials('username', 'password', 'example.org');
        $this->httpAuthenticationMiddleware->setHttpAuthenticationCredentials($credentials);

        $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
            function ($returnedRequest, $returnedOptions) use ($request, $options) {
                $this->assertEquals($request, $returnedRequest);
                $this->assertEquals($options, $returnedOptions);
            }
        );

        $returnedFunction($request, $options);
    }

    public function testInvokeCredentialsValidForRequest()
    {
        $modifiedRequest = \Mockery::mock(RequestInterface::class);

        $request = \Mockery::mock(RequestInterface::class);

        $request
            ->shouldReceive('getHeaderLine')
            ->with('host')
            ->andReturn('example.com');

        $request
            ->shouldReceive('withHeader')
            ->with(HttpAuthenticationHeader::NAME, 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=')
            ->andReturn($modifiedRequest);

        $options = [];

        $credentials = new HttpAuthenticationCredentials('username', 'password', 'example.com');
        $this->httpAuthenticationMiddleware->setHttpAuthenticationCredentials($credentials);

        $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
            function ($returnedRequest, $returnedOptions) use ($modifiedRequest, $options) {
                $this->assertEquals($modifiedRequest, $returnedRequest);
                $this->assertEquals($options, $returnedOptions);
            }
        );

        $returnedFunction($request, $options);
    }
}
