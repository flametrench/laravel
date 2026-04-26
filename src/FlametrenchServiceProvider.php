<?php

// Copyright 2026 NDC Digital, LLC
// SPDX-License-Identifier: Apache-2.0

declare(strict_types=1);

namespace Flametrench\Laravel;

use Flametrench\Authz\InMemoryTupleStore;
use Flametrench\Authz\TupleStore;
use Flametrench\Identity\IdentityStore;
use Flametrench\Identity\InMemoryIdentityStore;
use Flametrench\Tenancy\InMemoryTenancyStore;
use Flametrench\Tenancy\TenancyStore;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * Wires the four Flametrench SDK store interfaces into Laravel's
 * service container.
 *
 * Default bindings use the in-memory implementations — sufficient for
 * tests, local development, and prototypes. Production deployments
 * override the bindings to point at Postgres-backed stores; see the
 * "Production binding" section of the README.
 *
 * The package also publishes a config file at config/flametrench.php
 * that adopters can override per-deployment.
 */
final class FlametrenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/flametrench.php', 'flametrench');

        $this->app->singleton(IdentityStore::class, function (Application $app) {
            $driver = $app['config']->get('flametrench.identity.driver', 'in-memory');
            return match ($driver) {
                'in-memory' => new InMemoryIdentityStore(),
                default => throw new \RuntimeException(
                    "Unknown flametrench.identity.driver: {$driver}. "
                    . "Override the binding in your AppServiceProvider for a custom driver."
                ),
            };
        });

        $this->app->singleton(TenancyStore::class, function (Application $app) {
            $driver = $app['config']->get('flametrench.tenancy.driver', 'in-memory');
            return match ($driver) {
                'in-memory' => new InMemoryTenancyStore(),
                default => throw new \RuntimeException(
                    "Unknown flametrench.tenancy.driver: {$driver}. "
                    . "Override the binding in your AppServiceProvider for a custom driver."
                ),
            };
        });

        $this->app->singleton(TupleStore::class, function (Application $app) {
            $driver = $app['config']->get('flametrench.authz.driver', 'in-memory');
            return match ($driver) {
                'in-memory' => new InMemoryTupleStore(),
                default => throw new \RuntimeException(
                    "Unknown flametrench.authz.driver: {$driver}. "
                    . "Override the binding in your AppServiceProvider for a custom driver."
                ),
            };
        });

        $this->app->singleton(Flametrench::class, fn (Application $app) => new Flametrench(
            $app->make(IdentityStore::class),
            $app->make(TenancyStore::class),
            $app->make(TupleStore::class),
        ));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/flametrench.php' => $this->app->configPath('flametrench.php'),
            ], 'flametrench-config');
        }
    }
}
