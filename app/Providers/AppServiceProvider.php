<?php

namespace App\Providers;

use App\Models\Organization;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $division = Organization::has('childrens')->where('type', 'DIVISION')->where('name', 'DX - DIGITAL TRANSFORMATION')->first(['id']);
        View::share('division', $division);
    }
}
