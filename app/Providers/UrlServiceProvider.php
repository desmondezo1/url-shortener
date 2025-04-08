<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Interfaces\UrlServiceInterface;
use App\Services\UrlService;
use App\Services\Validation\UrlValidator;
use App\Services\Encoding\Base62Encoder;
use App\Services\Storage\InMemoryStore;
use App\Services\Cache\CacheManager;

class UrlServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the in-memory store as a singleton
        $this->app->singleton(InMemoryStore::class, function () {
            return new InMemoryStore();
        });

        // Register the cache manager
        $this->app->bind(CacheManager::class, function ($app) {
            return new CacheManager(
                $app['cache.store'],
                config('url.cache_ttl', 3600)
            );
        });

        // Register the URL validator
        $this->app->bind(UrlValidator::class, function () {
            return new UrlValidator();
        });

        // Register the encoder
        $this->app->bind(Base62Encoder::class, function () {
            return new Base62Encoder();
        });


        // Register the URL service
        $this->app->bind(UrlServiceInterface::class, function ($app) {
            return new UrlService(
                $app->make(UrlValidator::class),
                $app->make(Base62Encoder::class),
                $app->make(InMemoryStore::class),
                $app->make(CacheManager::class),
                config('url.base_url')
            );
        });
    }
}
