<?php

namespace App\Providers;

use App\Models\Chra;
use App\Models\Hirarc;
use App\Models\Nra;
use App\Policies\ChraPolicy;
use App\Policies\HirarcPolicy;
use App\Policies\NraPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Chra::class   => ChraPolicy::class,
        Hirarc::class => HirarcPolicy::class,
        Nra::class    => NraPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
