<?php

use kamermans\OAuth2\Token\RawToken;
use kamermans\OAuth2\Persistence\FileTokenPersistence;
use _2UpMedia\FreshbooksImporter\Services\Clients\FreshbooksFactory;
use _2UpMedia\FreshbooksImporter\Services\Clients\Freshbooks\RefreshTokenRequiredException;
use _2UpMedia\FreshbooksImporter\Services\Clients\Freshbooks;

Route::get( '/freshbooks-importer/oauth/redirect', function(\Illuminate\Http\Request $request, FreshbooksFactory $clientFactory) {
    try {
        $client = $clientFactory->build();
    } catch (RefreshTokenRequiredException $exception) {
        $tokenPath = config('freshbooks-importer.freshbooks.oauth2.persisted-token-path');

        $tokenPersistence = new FileTokenPersistence($tokenPath);
        $tokenPersistence->restoreToken(new RawToken());

        try {
            $accessTokenResponse = (new GuzzleHttp\Client())
                ->post(
                    Freshbooks::OAUTH_TOKEN_URL,
                    [
                        'json' => [
                            'grant_type' => 'authorization_code',
                            'client_id' => config('freshbooks-importer.freshbooks.oauth2.client-id'),
                            'client_secret' => config('freshbooks-importer.freshbooks.oauth2.client-secret'),
                            'code' => $request->query('code'),
                            'redirect_uri' => config('freshbooks-importer.freshbooks.oauth2.redirect-base-uri') . '/freshbooks-importer/oauth/redirect',
                        ],

                        'headers' => ['Api-Version' => 'alpha']
                    ]);

        } catch (\Exception $e) {
            $html = <<<HTML
<html>
<head>
<meta http-equiv="Refresh" content="5;url=/freshbooks-importer/oauth/authorize">
</head>
<body>
The code has expired. Re-authorizing you in 5 seconds.
</body>
</html>
HTML;

            return response($html);
        }

        $tokenResponse = json_decode($accessTokenResponse->getBody(), true);
        $tokenPersistence->saveToken(
            new RawToken(
                $tokenResponse['access_token'],
                $tokenResponse['refresh_token'],
                $tokenResponse['expires_in']
            )
        );
    }

    $message = 'Successfully authorized. You may now use the freshbooks-importer artisan commands.';

    return response($message);
});

Route::get( '/freshbooks-importer/oauth/authorize', function() {
    return redirect(config('freshbooks-importer.freshbooks.oauth2.authorize-url'));
});
