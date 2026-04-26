<?php

// Copyright 2026 NDC Digital, LLC
// SPDX-License-Identifier: Apache-2.0

declare(strict_types=1);

namespace Flametrench\Laravel;

use Flametrench\Authz\TupleStore;
use Flametrench\Identity\IdentityStore;
use Flametrench\Tenancy\TenancyStore;

/**
 * Single-handle accessor that routes to the three Flametrench stores.
 *
 * This is the class the {@see Facades\Flametrench} facade resolves to.
 * It exists primarily for facade ergonomics; controllers and jobs
 * SHOULD type-hint the underlying store interfaces directly
 * (`IdentityStore`, `TenancyStore`, `TupleStore`) when they only need
 * one capability — that gives clean dependency-injection signatures
 * and avoids hauling the full handle into one-purpose code paths.
 */
final readonly class Flametrench
{
    public function __construct(
        public IdentityStore $identity,
        public TenancyStore $tenancy,
        public TupleStore $authz,
    ) {}
}
