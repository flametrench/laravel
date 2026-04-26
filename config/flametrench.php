<?php

// Copyright 2026 NDC Digital, LLC
// SPDX-License-Identifier: Apache-2.0

declare(strict_types=1);

/**
 * Flametrench configuration.
 *
 * Each capability has a `driver` key. The default `in-memory` driver
 * wires the reference implementations from
 * flametrench/{identity,tenancy,authz} — useful for tests and
 * development. Production deployments override the driver and bind
 * a Postgres-backed (or otherwise persistent) store in their own
 * AppServiceProvider before this provider's `register()` runs.
 *
 * Adopters using ext-pgsql backends typically:
 *
 *   1. Set FLAMETRENCH_*_DRIVER=postgres in .env (the constants here
 *      read from those env vars).
 *   2. Override the singleton bindings in AppServiceProvider::register()
 *      to construct PostgresIdentityStore / PostgresTenancyStore /
 *      PostgresTupleStore (when those land in the SDKs) with the
 *      application's PDO / DSN.
 *
 * The Flametrench Laravel package does NOT itself ship Postgres-backed
 * stores — those live in the per-capability SDKs. This package's job
 * is the wiring.
 */
return [
    'identity' => [
        'driver' => env('FLAMETRENCH_IDENTITY_DRIVER', 'in-memory'),
    ],

    'tenancy' => [
        'driver' => env('FLAMETRENCH_TENANCY_DRIVER', 'in-memory'),
    ],

    'authz' => [
        'driver' => env('FLAMETRENCH_AUTHZ_DRIVER', 'in-memory'),
    ],
];
