<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/* Lớp HelperServiceProvider đăng ký tệp trợ giúp trong ứng dụng PHP. */
class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    /**
     * This function includes a helper file in a PHP application.
     * Chức năng này bao gồm một tệp trợ giúp trong ứng dụng PHP.
     */
    public function register()
    {
        require_once app_path() . '/Helpers/Helpers.php';
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
