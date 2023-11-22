<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/* Lớp RouteServiceProvider định cấu hình giới hạn tốc độ cho các yêu cầu API và đặt giá trị mặc định
không gian tên cho bộ điều khiển trong ứng dụng Laravel. */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
   
    /* `protected = 'App\Http\Controllers';` đang đặt không gian tên mặc định cho bộ điều khiển
    các lớp học. Điều này có nghĩa là khi xác định tuyến đường, nếu bộ điều khiển không được chỉ định với
    không gian tên, nó sẽ mặc định là không gian tên được chỉ định ở đây. Trong trường hợp này, không gian tên được đặt
    thành `App\Http\Controllers`, là không gian tên mặc định cho các bộ điều khiển trong Laravel
    ứng dụng. */
    protected $namespace = 'App\Http\Controllers';
    //public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    
/**
     * Chức năng này thiết lập cấu hình giới hạn tốc độ và xác định các tuyến cho API và web
     * phần mềm trung gian trong ứng dụng Laravel.
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
            ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    /**
     * Chức năng này định cấu hình giới hạn tốc độ cho các yêu cầu API dựa trên ID hoặc địa chỉ IP của người dùng
     * 
     * @return Bộ giới hạn tốc độ đang được định cấu hình cho khóa 'api'. Bộ giới hạn tốc độ được đặt để giới hạn các yêu cầu ở mức 60 mỗi phút và nó đang được áp dụng cho ID người dùng nếu có hoặc địa chỉ IP nếu không. Hàm này không trả về bất kỳ thứ gì, nó chỉ thiết lập bộ giới hạn tốc độ.
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
