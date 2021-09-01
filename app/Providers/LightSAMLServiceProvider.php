<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LightSaml\Binding\BindingFactory;
use LightSaml\Build\Container\BuildContainerInterface;
use LightSaml\Build\Container\CredentialContainerInterface;
use LightSaml\Build\Container\ServiceContainerInterface;
use LightSaml\Store\Credential\CompositeCredentialStore;
use LightSaml\Store\Credential\CredentialStoreInterface;
use LightSaml\Store\Credential\X509FileCredentialStore;

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
        $this->app->bind(BuildContainerInterface::class, function () {
            return new class implements BuildContainerInterface {
                public function getSystemContainer()
                {
                    // TODO: Implement getSystemContainer() method.
                }

                public function getPartyContainer()
                {
                    // TODO: Implement getPartyContainer() method.
                }

                public function getStoreContainer()
                {
                    // TODO: Implement getStoreContainer() method.
                }

                public function getProviderContainer()
                {
                    // TODO: Implement getProviderContainer() method.
                }

                public function getCredentialContainer()
                {
                    return new class implements CredentialContainerInterface {
                        public function getCredentialStore()
                        {
                            return app(CredentialStoreInterface::class);
                        }
                    };
                }

                public function getServiceContainer()
                {
                    return app(ServiceContainerInterface::class);
                }

                public function getOwnContainer()
                {
                    // TODO: Implement getOwnContainer() method.
                }
            };
        });
        $this->app->bind(CredentialStoreInterface::class, function () {
            $store = new CompositeCredentialStore();
            $store->add(new X509FileCredentialStore('sp', storage_path('app/saml/credentials/sp.crt'), storage_path('app/saml/credentials/sp.pem'), null));
            $store->add(new X509FileCredentialStore('idp', storage_path('app/saml/credentials/idp.crt'), storage_path('app/saml/credentials/idp.pem'), null));

            return $store;
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
