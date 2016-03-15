<?php

namespace Mytdt\Auth0\Lumen\Providers;

use Illuminate\Support\ServiceProvider;
use RuntimeException;

class Auth0ServiceProvider extends ServiceProvider
{
    /**
     * Setup the configuration.
     * 
     * @throws RuntimeException if not running in console and also not configured AUTH0 domain, client_id and client_secret.
     */
    protected function setupConfig()
    {
        $this->app->configure('auth0');

        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/auth0.php'), 'auth0');

        $config = $this->app['config']['auth0'];

        if (!$this->app->runningInConsole() && empty($config['domain']) && empty($config['clientId']) && empty($config['clientSecret'])) {
            throw new RuntimeException('Unable to boot Auth0ServiceProvider, configure an AUTH0 domain, client_id and client_secret.');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->setupConfig();
    }
}
