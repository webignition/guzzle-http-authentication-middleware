<?php

namespace webignition\Guzzle\Middleware\HttpAuthentication\Tests;

use Mockery\MockInterface;
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
        $request = $this->createOriginalRequest();
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

    public function testInvokeValidCredentialsAppliedToAllRequests()
    {
        $credentials = new HttpAuthenticationCredentials('username', 'password', 'example.com');
        $this->httpAuthenticationMiddleware->setHttpAuthenticationCredentials($credentials);

        $requestCount = 3;

        $modifiedRequest = \Mockery::mock(RequestInterface::class);
        $originalRequest = $this->createOriginalRequest();
        $originalRequest
            ->shouldReceive('withHeader')
            ->with(HttpAuthenticationHeader::NAME, 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=')
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

    public function testInvokeValidCredentialsAppliedToFirstRequestOnly()
    {
        $credentials = new HttpAuthenticationCredentials('username', 'password', 'example.com');
        $this->httpAuthenticationMiddleware->setHttpAuthenticationCredentials($credentials);
        $this->httpAuthenticationMiddleware->setIsSingleUse(true);

        $requestCount = 3;
        $modifiedRequest = \Mockery::mock(RequestInterface::class);
        $originalRequest = $this->createOriginalRequest();

        for ($requestIndex = 0; $requestIndex < $requestCount; $requestIndex++) {
            if ($requestIndex === 0) {
                $originalRequest
                    ->shouldReceive('withHeader')
                    ->with(HttpAuthenticationHeader::NAME, 'Basic dXNlcm5hbWU6cGFzc3dvcmQ=')
                    ->andReturn($modifiedRequest);
            }

            $options = [];

            $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
                function (
                    $returnedRequest,
                    $returnedOptions
                ) use (
                    $originalRequest,
                    $modifiedRequest,
                    $options,
                    $requestIndex
                ) {
                    if ($requestIndex === 0) {
                        $this->assertEquals($modifiedRequest, $returnedRequest);
                    } else {
                        $this->assertEquals($originalRequest, $returnedRequest);
                    }

                    $this->assertEquals($options, $returnedOptions);
                }
            );

            $returnedFunction($originalRequest, $options);
        }
    }

    /**
     * @return MockInterface|RequestInterface
     */
    private function createOriginalRequest()
    {
        $request = \Mockery::mock(RequestInterface::class);
        $request
            ->shouldReceive('getHeaderLine')
            ->with('host')
            ->andReturn('example.com');

        return $request;
    }
}
