<?php

namespace App\Providers;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
       // Gate::policy(Procedure::class, ProcedurePolicy::class);
    }
}