<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auth0 Domain
    |--------------------------------------------------------------------------
    |
    | Your domain choosen within Auth0. It can be viewed on Auth0 Dashboard.
    | For example: yourname.auth0.com
    |
    */
    'domain' => env('AUTH0_DOMAIN', ''),

    /*
    |--------------------------------------------------------------------------
    | Auth0 Client ID
    |--------------------------------------------------------------------------
    |
    | The Client ID of your application on Auth0 Dashboard.
    |
    */
    'clientId' => env('AUTH0_CLIENT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Auth0 Client Secret
    |--------------------------------------------------------------------------
    |
    | The Client Secret of your application on Auth0 Dashboard.
    |
    */
    'clientSecret' => env('AUTH0_CLIENT_SECRET', ''),

];