<?php
/**
 * Created by PhpStorm.
 * User: x2UP_Media
 * Date: 3/18/18
 * Time: 7:57 PM
 */
namespace _2UpMedia\FreshbooksImporter\Services\Clients\Freshbooks;

use _2UpMedia\FreshbooksImporter\Services\Clients\Freshbooks;
use GuzzleHttp\HandlerStack;

use _2UpMedia\FreshbooksImporter\OauthSubscriber\GrantType\RefreshToken;
use kamermans\OAuth2\Persistence\FileTokenPersistence;
use kamermans\OAuth2\Token\RawToken;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\GrantType\NullGrantType;

use GuzzleHttp;
use Psr\Http\Message\RequestInterface;

class ClientFactory
{
    public function build(string $refreshTokenValue = null) {
        $tokenPath = config('freshbooks-importer.freshbooks.oauth2.persisted-token-path');
        $tokenPersistence = new FileTokenPersistence($tokenPath);

        $persistedToken = $tokenPersistence->restoreToken(new RawToken());

        if ($refreshTokenValue === null && ! $persistedToken) {
            throw new RefreshTokenRequiredException();
        }

        $refreshToken = new RefreshToken(
            new GuzzleHttp\Client([
                'base_uri' => Freshbooks::OAUTH_TOKEN_URL,
            ]),
            [
                'client_id' => config('freshbooks-importer.freshbooks.oauth2.client-id'),
                'client_secret' => config('freshbooks-importer.freshbooks.oauth2.client-secret'),
                'refresh_token' => $refreshTokenValue ?? $persistedToken->getRefreshToken(),
            ]
        );

        $oauth = new OAuth2Middleware(new NullGrantType(), $refreshToken);
        $oauth->setTokenPersistence($tokenPersistence);

        $stack = HandlerStack::create();
        $stack->push($oauth);
        $stack->push(GuzzleHttp\Middleware::mapRequest(function (RequestInterface $request) {
            return $request->withHeader('Api-Version', 'alpha');
        }));

        $client = new GuzzleHttp\Client([
            'handler' => $stack,
            'auth'    => 'oauth',
        ]);

        return $client;
    }
}