<?php
/**
 * @copyright (c) 2016 Guild Mortgage Company. All rights reserved
 * @author Jorge Colon <george@alanseiden.com>
 */

namespace _2UpMedia\FreshbooksImporter\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class OauthCallbackRouteProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = '_2UpMedia\FreshbooksImporter\Web\Controllers';

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $router->group([
            'namespace' => $this->namespace,
            'middleware' => 'web',
        ], function ($router) {
            require __DIR__.'/../Web/routes.php';
        });
    }
}
