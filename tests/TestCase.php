<?php

// Copyright 2026 NDC Digital, LLC
// SPDX-License-Identifier: Apache-2.0

declare(strict_types=1);

namespace Tests;

use Flametrench\Laravel\FlametrenchServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Base test case that boots a Laravel application via Orchestra Testbench
 * with the Flametrench service provider registered. Pest tests reference
 * this via tests/Pest.php.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @return array<int,class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            FlametrenchServiceProvider::class,
        ];
    }
}
