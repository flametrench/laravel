<?php

// Copyright 2026 NDC Digital, LLC
// SPDX-License-Identifier: Apache-2.0

declare(strict_types=1);

namespace Flametrench\Laravel\Facades;

use Flametrench\Authz\TupleStore;
use Flametrench\Identity\IdentityStore;
use Flametrench\Tenancy\TenancyStore;
use Illuminate\Support\Facades\Facade;

/**
 * @method static IdentityStore identity()
 * @method static TenancyStore tenancy()
 * @method static TupleStore authz()
 *
 * Static-call shorthand routed at runtime to {@see \Flametrench\Laravel\Flametrench}.
 *
 * @see \Flametrench\Laravel\Flametrench
 */
final class Flametrench extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Flametrench\Laravel\Flametrench::class;
    }
}
