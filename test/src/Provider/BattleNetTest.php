<?php

namespace Madmatt\OAuth2\Client\Test\Provider;

use Madmatt\OAuth2\Client\Provider\BattleNet;

class BattleNetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BattleNet
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new BattleNet([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'region' => 'apac',
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Please specify a valid Battle.net region.
     */
    public function testNotPassingRegionThrowsException()
    {
        $provider = new BattleNet([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Please specify a valid Battle.net region (given invalid, allowed one of apac, cn, eu, us).
     */
    public function testPassingInvalidRegionThrowsException()
    {
        $provider = new BattleNet([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'region' => 'invalid'
        ]);
    }

    public function testAuthorizationUrl()
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        // Custom Battle.net tests
        $this->assertSame('apac.battle.net', $uri['host']);
        $this->assertSame('/oauth/authorize', $uri['path']);

        // Standard OAuth2 tests
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());
    }

    public function testGetBaseAuthorizationUrl()
    {
        $this->provider->setRegion('apac');
        $this->assertSame(
            'https://apac.battle.net/oauth/authorize',
            $this->provider->getBaseAuthorizationUrl()
        );

        $this->provider->setRegion('apac');
        $this->assertStringStartsWith('https://apac.battle.net/', $this->provider->getBaseAuthorizationUrl());

        $this->provider->setRegion('cn');
        $this->assertStringStartsWith('https://www.battlenet.com.cn', $this->provider->getBaseAuthorizationUrl());

        $this->provider->setRegion('eu');
        $this->assertStringStartsWith('https://eu.battle.net/', $this->provider->getBaseAuthorizationUrl());

        $this->provider->setRegion('us');
        $this->assertStringStartsWith('https://us.battle.net/', $this->provider->getBaseAuthorizationUrl());
    }

    public function testGetBaseAccessTokenUrl()
    {
        $this->provider->setRegion('apac');
        $this->assertSame(
            'https://apac.battle.net/oauth/token',
            $this->provider->getBaseAccessTokenUrl([])
        );

        $this->provider->setRegion('apac');
        $this->assertStringStartsWith('https://apac.battle.net/', $this->provider->getBaseAccessTokenUrl([]));

        $this->provider->setRegion('cn');
        $this->assertStringStartsWith('https://www.battlenet.com.cn', $this->provider->getBaseAccessTokenUrl([]));

        $this->provider->setRegion('eu');
        $this->assertStringStartsWith('https://eu.battle.net/', $this->provider->getBaseAccessTokenUrl([]));

        $this->provider->setRegion('us');
        $this->assertStringStartsWith('https://us.battle.net/', $this->provider->getBaseAccessTokenUrl([]));
    }
}