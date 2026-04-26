<?php

// Copyright 2026 NDC Digital, LLC
// SPDX-License-Identifier: Apache-2.0

declare(strict_types=1);

use Flametrench\Authz\InMemoryTupleStore;
use Flametrench\Authz\TupleStore;
use Flametrench\Identity\IdentityStore;
use Flametrench\Identity\InMemoryIdentityStore;
use Flametrench\Laravel\Facades\Flametrench as FlametrenchFacade;
use Flametrench\Laravel\Flametrench;
use Flametrench\Tenancy\InMemoryTenancyStore;
use Flametrench\Tenancy\TenancyStore;

it('binds IdentityStore to InMemoryIdentityStore by default', function () {
    $store = $this->app->make(IdentityStore::class);
    expect($store)->toBeInstanceOf(InMemoryIdentityStore::class);
});

it('binds TenancyStore to InMemoryTenancyStore by default', function () {
    $store = $this->app->make(TenancyStore::class);
    expect($store)->toBeInstanceOf(InMemoryTenancyStore::class);
});

it('binds TupleStore to InMemoryTupleStore by default', function () {
    $store = $this->app->make(TupleStore::class);
    expect($store)->toBeInstanceOf(InMemoryTupleStore::class);
});

it('binds the Flametrench handle as a singleton', function () {
    $a = $this->app->make(Flametrench::class);
    $b = $this->app->make(Flametrench::class);
    expect($a)->toBe($b);
    expect($a->identity)->toBeInstanceOf(IdentityStore::class);
    expect($a->tenancy)->toBeInstanceOf(TenancyStore::class);
    expect($a->authz)->toBeInstanceOf(TupleStore::class);
});

it('exposes the Flametrench facade routed to the singleton', function () {
    $store = FlametrenchFacade::getFacadeRoot();
    expect($store)->toBeInstanceOf(Flametrench::class);
});

it('publishes the config file', function () {
    expect(config('flametrench'))->toBeArray()
        ->toHaveKeys(['identity', 'tenancy', 'authz']);
    expect(config('flametrench.identity.driver'))->toBe('in-memory');
    expect(config('flametrench.tenancy.driver'))->toBe('in-memory');
    expect(config('flametrench.authz.driver'))->toBe('in-memory');
});

it('throws on an unknown identity driver', function () {
    config(['flametrench.identity.driver' => 'mystery']);
    // Re-resolve to trigger the binding closure.
    $this->app->forgetInstance(IdentityStore::class);
    $this->app->make(IdentityStore::class);
})->throws(\RuntimeException::class, 'Unknown flametrench.identity.driver: mystery');

it('throws on an unknown tenancy driver', function () {
    config(['flametrench.tenancy.driver' => 'mystery']);
    $this->app->forgetInstance(TenancyStore::class);
    $this->app->make(TenancyStore::class);
})->throws(\RuntimeException::class, 'Unknown flametrench.tenancy.driver: mystery');

it('throws on an unknown authz driver', function () {
    config(['flametrench.authz.driver' => 'mystery']);
    $this->app->forgetInstance(TupleStore::class);
    $this->app->make(TupleStore::class);
})->throws(\RuntimeException::class, 'Unknown flametrench.authz.driver: mystery');

it('lets the application override the IdentityStore binding', function () {
    // Adopters with their own Postgres-backed store register it BEFORE
    // this provider runs (typical pattern: in AppServiceProvider::register
    // with `singleton(IdentityStore::class, fn() => new MyStore())`).
    // Confirm that an existing binding is preserved.
    $custom = new InMemoryIdentityStore();
    $this->app->forgetInstance(IdentityStore::class);
    $this->app->instance(IdentityStore::class, $custom);
    expect($this->app->make(IdentityStore::class))->toBe($custom);
});
