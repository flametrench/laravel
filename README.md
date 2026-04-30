# Flametrench Laravel SDK

Laravel adapter for [Flametrench](https://github.com/flametrench/spec) — wires the four PHP SDK packages (`flametrench/{ids,identity,tenancy,authz}`) into Laravel's service container.

## Status

Pre-release scaffold. Targets Laravel 11+ on PHP 8.3+. Spec tracking [v0.2.0](https://github.com/flametrench/spec/releases).

## Install

```bash
composer require flametrench/laravel:^0.2.0
```

The package auto-registers `Flametrench\Laravel\FlametrenchServiceProvider` and the `Flametrench` facade alias via Laravel's package discovery.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=flametrench-config
```

This drops `config/flametrench.php` into your app, where you select drivers per capability:

```php
return [
    'identity' => ['driver' => env('FLAMETRENCH_IDENTITY_DRIVER', 'in-memory')],
    'tenancy'  => ['driver' => env('FLAMETRENCH_TENANCY_DRIVER',  'in-memory')],
    'authz'    => ['driver' => env('FLAMETRENCH_AUTHZ_DRIVER',    'in-memory')],
];
```

The default `in-memory` driver wires the reference implementations from the per-capability SDKs — fine for tests and local development.

## Production binding

This package does not ship Postgres-backed stores; those live in the per-capability SDKs as they land. For production, override the bindings in your own `AppServiceProvider::register()`:

```php
use Flametrench\Identity\IdentityStore;
use App\Identity\PostgresIdentityStore;

public function register(): void
{
    $this->app->singleton(IdentityStore::class, function ($app) {
        return new PostgresIdentityStore($app->make(\PDO::class));
    });
}
```

`AppServiceProvider` runs before package providers, so your binding wins.

## Usage

Type-hint the Flametrench store interfaces in controller / job constructors:

```php
use Flametrench\Tenancy\TenancyStore;

class OrgController
{
    public function __construct(private readonly TenancyStore $tenancy) {}

    public function store(Request $request): JsonResponse
    {
        $result = $this->tenancy->createOrg(
            $request->user()->id,
            name: $request->input('name'),
            slug: $request->input('slug'),
        );
        return response()->json($result['org']);
    }
}
```

Or use the `Flametrench` facade for one-liners:

```php
use Flametrench\Laravel\Facades\Flametrench;

$user = Flametrench::identity()->getUser($usrId);
```

## Testing

```bash
composer install
composer test
composer test:coverage
```

The test suite uses [Orchestra Testbench](https://github.com/orchestral/testbench) to boot a Laravel application around the package, so the service provider binds against a real container.

## License

Apache License 2.0. Copyright 2026 NDC Digital, LLC.
