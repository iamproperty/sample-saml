<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LightSaml\Binding\BindingFactory;
use LightSaml\Build\Container\ServiceContainerInterface;
use LightSaml\Event\Events;
use Symfony\Component\EventDispatcher\EventDispatcher;

class LightSAMLServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ServiceContainerInterface::class, function () {
            return new class implements ServiceContainerInterface {
                public function getAssertionValidator()
                {
                    // TODO: Implement getAssertionValidator() method.
                }

                public function getAssertionTimeValidator()
                {
                    // TODO: Implement getAssertionTimeValidator() method.
                }

                public function getSignatureResolver()
                {
                    // TODO: Implement getSignatureResolver() method.
                }

                public function getEndpointResolver()
                {
                    // TODO: Implement getEndpointResolver() method.
                }

                public function getNameIdValidator()
                {
                    // TODO: Implement getNameIdValidator() method.
                }

                public function getBindingFactory()
                {
                    return new BindingFactory();
                }

                public function getSignatureValidator()
                {
                    // TODO: Implement getSignatureValidator() method.
                }

                public function getCredentialResolver()
                {
                    // TODO: Implement getCredentialResolver() method.
                }

                public function getLogoutSessionResolver()
                {
                    // TODO: Implement getLogoutSessionResolver() method.
                }

                public function getSessionProcessor()
                {
                    // TODO: Implement getSessionProcessor() method.
                }

            };
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
