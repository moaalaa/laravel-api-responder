<?php

namespace MoaAlaa\ApiResponder;

use Illuminate\Support\ServiceProvider;


class ApiResponderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    public function register()
    {
        
    }
}