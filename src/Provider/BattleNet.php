<?php

namespace Madmatt\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class BattleNet extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var array Mapping of Battle.net regions to the base URL for that region
     */
    const REGIONALISED_BASE_URLS = [
        'apac' => 'https://apac.battle.net',
        'cn' => 'https://www.battlenet.com.cn',
        'eu' => 'https://eu.battle.net',
        'us' => 'https://us.battle.net',
    ];

    /**
     * @var string Key used in a token response to identify the resource owner.
     * @todo accountId isn't unique, it's just unique within partitions. This should be <partitionId>:<accountId>
     * @todo Look to override getAccessTokenResourceOwnerId() instead
     */
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'accountId';

    /**
     * @var string One of the Battle.net regions. Valid regions are apac, cn, eu, us
     * @see self::REGIONALISED_BASE_URLS
     */
    protected $region;

    public function __construct(array $options = [], array $collaborators = [])
    {
        $region = isset($options['region']) ? $options['region'] : '';
        $this->setRegion($region);
        unset($options['region']);

        parent::__construct($options, $collaborators);
    }


    public function setRegion($region = '')
    {
        if (!is_string($region) || empty($region)) {
            throw new \InvalidArgumentException('Please specify a valid Battle.net region.');
        } elseif (!in_array($region, array_keys(self::REGIONALISED_BASE_URLS))) {
            $message = sprintf(
                'Please specify a valid Battle.net region (given %s, allowed one of %s).',
                $region,
                join(', ', array_keys(self::REGIONALISED_BASE_URLS))
            );

            throw new \InvalidArgumentException($message);
        }

        $this->region = $region;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBaseAuthorizationUrl()
    {
        return sprintf('%s/oauth/authorize', self::REGIONALISED_BASE_URLS[$this->region]);
    }

    /**
     * @inheritdoc
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return sprintf('%s/oauth/token', self::REGIONALISED_BASE_URLS[$this->region]);
    }

    /**
     * Returns the URL for requesting the resource owner's details.
     *
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $test = 1;
    }

    /**
     * Returns the default scopes used by this provider.
     *
     * This should only be the scopes that are required to request the details
     * of the resource owner, rather than all the available scopes.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        // TODO: Implement this game-agnostic, though there's no shared scope for standard profile data unfortunately
        return [
            'wow.profile',
            'sc2.profile'
        ];
    }

    /**
     * Checks a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  array|string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        $test = '2';
    }

    /**
     * Generates a resource owner object from a successful resource owner
     * details request.
     *
     * @param  array $response
     * @param  AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        $test = 3;
    }

    /**
     * @inheritdoc
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }
}