<?php

namespace Dotburo\Molog\Tests;

use Dotburo\Molog\MologServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /** @inheritdoc */
    protected function defineRoutes($router)
    {
        $router->get('messages', '\Dotburo\Molog\Http\Controllers\MessageController@index');
    }

    /** @inheritdoc */
    protected function getPackageProviders($app)
    {
        return [
            MologServiceProvider::class,
        ];
    }

    /** @inheritdoc */
    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
