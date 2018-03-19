<?php

return [
    'freshbooks-classic' => [
        'harvest' => [
            'projects-mappings' => [
                '[Harvest Project Name]' => '[Mapped Freshbooks Project ID]',
            ],
            'tasks-mappings' => [
                '[Harvest Task Name]' => '[Mapped Freshbooks Task ID]',
            ]
        ],
        'subdomain' => '[Freshbooks subdomain]',
        'authentication-token' => ''
    ],
    'freshbooks' => [
        'harvest' => [
            'project-mappings' => [
                '[Harvest Project Name]' => '[Mapped Freshbooks Project ID]',
            ],
            'clients-mappings' => [
                '[Harvest Client Name]' => '[Mapped Freshbooks Client ID]',
            ]
        ],
        'business-id' => '', // run ./artisan fresh-importer:list-accounts to find this out
        'account-id' => '',
        'oauth2' => [
            'client-id' => '',
            'client-secret' => '',
            'redirect-base-uri' => '', // use ngrok to get our local ./artisan serve dev server exposed with SSL which is required from Freshbooks. This will call https://RANDOM_ID.ngrok.io/freshbooks-importer/oauth/redirect.
            'persisted-token-path' => storage_path().'/app/freshbooks-importer-access_token.json',
            'authorize-url' => '' // after creating app copy/paste "Authorization URL" https://my.freshbooks.com/#/developer
        ],
    ],
    'freskbooks-version' => '' // freshbooks or freshbooks-classic
];
