<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

/* Lớp ComposerServiceProvider đăng ký và khởi động chế độ xem trình soạn cho tiêu đề và minicart
bố cục trong ứng dụng Laravel. */
class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
   /**
    * Hàm này thiết lập trình soạn thảo chế độ xem cho bố cục tiêu đề và minicart trong ứng dụng Laravel.
    */
    public function boot()
    {
        //
        View::composer(
            'layouts.header', 'App\Http\ViewComposers\HeaderComposer'
        );
        View::composer(
            'layouts.minicart', 'App\Http\ViewComposers\CartComposer'
        );
    }
}
