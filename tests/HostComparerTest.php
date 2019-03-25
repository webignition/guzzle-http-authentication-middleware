<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\Guzzle\Middleware\HttpAuthentication\Tests;

use webignition\Guzzle\Middleware\HttpAuthentication\HostComparer;

class HostComparerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var HostComparer
     */
    private $hostComparer;

    protected function setUp()
    {
        parent::setUp();

        $this->hostComparer = new HostComparer();
    }

    /**
     * @dataProvider isHostMatchDataProvider
     */
    public function testIsHostMatch(string $requestHost, string $authenticationHost, bool $expectedIsHostMatch)
    {
        $this->assertEquals($expectedIsHostMatch, $this->hostComparer->isHostMatch($requestHost, $authenticationHost));
    }

    public function isHostMatchDataProvider(): array
    {
        return [
            'lowercase equality' => [
                'requestHost' => 'example.com',
                'authenticationHost' => 'example.com',
                'expectedIsHostMatch' => true,
            ],
            'uppercase equality' => [
                'requestHost' => 'EXAMPLE.COM',
                'authenticationHost' => 'EXAMPLE.COM',
                'expectedIsHostMatch' => true,
            ],
            'authentication host is ending substring of request host' => [
                'requestHost' => 'subdomain.example.com',
                'authenticationHost' => 'example.com',
                'expectedIsHostMatch' => true,
            ],
            'simple inequality' => [
                'requestHost' => 'example.com',
                'authenticationHost' => 'example.org',
                'expectedIsHostMatch' => false,
            ],
            'authentication host is middle substring of request host' => [
                'requestHost' => 'subdomain.example.com.org',
                'authenticationHost' => 'example.com',
                'expectedIsHostMatch' => false,
            ],
        ];
    }
}
