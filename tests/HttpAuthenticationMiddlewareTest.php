<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication\Tests;

use Mockery\MockInterface;
use Psr\Http\Message\RequestInterface;
use webignition\Guzzle\Middleware\HttpAuthentication\AuthorizationType;
use webignition\Guzzle\Middleware\HttpAuthentication\BasicCredentials;
use webignition\Guzzle\Middleware\HttpAuthentication\AuthorizationHeader;
use webignition\Guzzle\Middleware\HttpAuthentication\HttpAuthenticationMiddleware;

class HttpAuthenticationMiddlewareTest extends \PHPUnit\Framework\TestCase
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

    public function testInvokeTypeNotSet()
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

    public function testInvokeCredentialsNotSet()
    {
        $request = \Mockery::mock(RequestInterface::class);
        $options = [];

        $this->httpAuthenticationMiddleware->setType(AuthorizationType::BASIC);

        $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
            function ($returnedRequest, $returnedOptions) use ($request, $options) {
                $this->assertEquals($request, $returnedRequest);
                $this->assertEquals($options, $returnedOptions);
            }
        );

        $returnedFunction($request, $options);
    }

    public function testInvokeEmptyCredentials()
    {
        $request = $this->createOriginalRequest();
        $options = [];

        $credentials = new BasicCredentials();
        $this->httpAuthenticationMiddleware->setType(AuthorizationType::BASIC);
        $this->httpAuthenticationMiddleware->setCredentials($credentials);

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
        $request = $this->createOriginalRequest();
        $options = [];

        $credentials = new BasicCredentials('username', 'password', 'example.org');
        $this->httpAuthenticationMiddleware->setType(AuthorizationType::BASIC);
        $this->httpAuthenticationMiddleware->setCredentials($credentials);

        $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
            function ($returnedRequest, $returnedOptions) use ($request, $options) {
                $this->assertEquals($request, $returnedRequest);
                $this->assertEquals($options, $returnedOptions);
            }
        );

        $returnedFunction($request, $options);
    }

    public function testInvokeValidCredentialsAppliedToAllRequests()
    {
        $credentials = new BasicCredentials('username', 'password', 'example.com');
        $this->httpAuthenticationMiddleware->setType(AuthorizationType::BASIC);
        $this->httpAuthenticationMiddleware->setCredentials($credentials);

        $requestCount = 3;

        $modifiedRequest = \Mockery::mock(RequestInterface::class);
        $originalRequest = $this->createOriginalRequest();
        $originalRequest
            ->shouldReceive('withHeader')
            ->with(AuthorizationHeader::NAME, 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=')
            ->andReturn($modifiedRequest);

        for ($requestIndex = 0; $requestIndex < $requestCount; $requestIndex++) {
            $options = [];

            $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
                function ($returnedRequest, $returnedOptions) use ($modifiedRequest, $options) {
                    $this->assertEquals($modifiedRequest, $returnedRequest);
                    $this->assertEquals($options, $returnedOptions);
                }
            );

            $returnedFunction($originalRequest, $options);
        }
    }

    /**
     * @return MockInterface|RequestInterface
     */
    private function createOriginalRequest(): RequestInterface
    {
        $request = \Mockery::mock(RequestInterface::class);
        $request
            ->shouldReceive('getHeaderLine')
            ->with('host')
            ->andReturn('example.com');

        return $request;
    }
}
