<?php

namespace App\Providers;

use App\Models\Chra;
use App\Policies\ChraPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Chra::class => ChraPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
