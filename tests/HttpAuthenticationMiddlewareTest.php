<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\Guzzle\Middleware\HttpAuthentication\Tests;

use Mockery\MockInterface;
use Psr\Http\Message\RequestInterface;
use webignition\Guzzle\Middleware\HttpAuthentication\AuthorizationType;
use webignition\Guzzle\Middleware\HttpAuthentication\AuthorizationHeader;
use webignition\Guzzle\Middleware\HttpAuthentication\CredentialsFactory;
use webignition\Guzzle\Middleware\HttpAuthentication\HostComparer;
use webignition\Guzzle\Middleware\HttpAuthentication\HttpAuthenticationMiddleware;

class HttpAuthenticationMiddlewareTest extends \PHPUnit\Framework\TestCase
{
    const HOST = 'example.com';

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

        $this->httpAuthenticationMiddleware = new HttpAuthenticationMiddleware(new HostComparer());
    }

    /**
     * @dataProvider invokeAuthorizationNotSetDataProvider
     */
    public function testInvokeAuthorizationNotSet(
        string $requestHost,
        ?string $type,
        ?string $credentials,
        ?string $host
    ) {
        $request = \Mockery::mock(RequestInterface::class);
        $request
            ->shouldReceive('getHeaderLine')
            ->with('host')
            ->andReturn($requestHost);

        $options = [];

        if (null !== $type) {
            $this->httpAuthenticationMiddleware->setType(AuthorizationType::BASIC);
        }

        if (null !== $credentials) {
            $this->httpAuthenticationMiddleware->setCredentials($credentials);
        }

        if (null !== $host) {
            $this->httpAuthenticationMiddleware->setHost($host);
        }

        $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
            function ($returnedRequest, $returnedOptions) use ($request, $options) {
                $this->assertEquals($request, $returnedRequest);
                $this->assertEquals($options, $returnedOptions);
            }
        );

        $returnedFunction($request, $options);
    }

    public function invokeAuthorizationNotSetDataProvider(): array
    {
        return [
            'no properties set' => [
                'requestHost' => self::HOST,
                'type' => null,
                'credentials' => null,
                'host' => null,
            ],
            'type set, no credentials, no host' => [
                'requestHost' => self::HOST,
                'type' => AuthorizationType::BASIC,
                'credentials' => null,
                'host' => null,
            ],
            'type set, credentials set, no host' => [
                'requestHost' => self::HOST,
                'type' => AuthorizationType::BASIC,
                'credentials' => 'non-blank string',
                'host' => null,
            ],
            'empty credentials' => [
                'requestHost' => self::HOST,
                'type' => AuthorizationType::BASIC,
                'credentials' => '',
                'host' => self::HOST,
            ],
            'host mismatch' => [
                'requestHost' => self::HOST,
                'type' => AuthorizationType::BASIC,
                'credentials' => 'non-blank string',
                'host' => 'foo' . self::HOST,
            ],
        ];
    }

    public function testInvokeValidCredentialsAppliedToAllRequests()
    {
        $credentials = CredentialsFactory::createBasicCredentials('username', 'password');

        $this->httpAuthenticationMiddleware->setType(AuthorizationType::BASIC);
        $this->httpAuthenticationMiddleware->setCredentials($credentials);
        $this->httpAuthenticationMiddleware->setHost(self::HOST);

        $requestCount = 3;

        $modifiedRequest = \Mockery::mock(RequestInterface::class);
        $originalRequest = $this->createOriginalRequest();
        $originalRequest
            ->shouldReceive('withHeader')
            ->with(AuthorizationHeader::NAME, 'Basic ' . $credentials)
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

    public function testClearTypeClearCredentials()
    {
        $credentials = CredentialsFactory::createBasicCredentials('username', 'password');

        $this->httpAuthenticationMiddleware->setType(AuthorizationType::BASIC);
        $this->httpAuthenticationMiddleware->setCredentials($credentials);
        $this->httpAuthenticationMiddleware->setHost(self::HOST);

        $modifiedRequest = \Mockery::mock(RequestInterface::class);
        $originalRequest = $this->createOriginalRequest();
        $originalRequest
            ->shouldReceive('withHeader')
            ->with(AuthorizationHeader::NAME, 'Basic ' . $credentials)
            ->andReturn($modifiedRequest);

        $options = [];

        $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
            function ($returnedRequest, $returnedOptions) use ($modifiedRequest, $options) {
                $this->assertEquals($modifiedRequest, $returnedRequest);
                $this->assertEquals($options, $returnedOptions);
            }
        );

        $returnedFunction($originalRequest, $options);

        $this->httpAuthenticationMiddleware->clearType();
        $this->httpAuthenticationMiddleware->clearCredentials();

        $returnedFunction = $this->httpAuthenticationMiddleware->__invoke(
            function ($returnedRequest, $returnedOptions) use ($originalRequest, $options) {
                $this->assertEquals($originalRequest, $returnedRequest);
                $this->assertEquals($options, $returnedOptions);
            }
        );

        $returnedFunction($originalRequest, $options);
    }

    /**
     * @return MockInterface|RequestInterface
     */
    private function createOriginalRequest(string $host = self::HOST): RequestInterface
    {
        $request = \Mockery::mock(RequestInterface::class);
        $request
            ->shouldReceive('getHeaderLine')
            ->with('host')
            ->andReturn($host);

        return $request;
    }
}
