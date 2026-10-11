<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // 1. Tambahkan import ini

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 2. Tambahkan baris ini
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}