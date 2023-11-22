<?php

namespace App\Http;

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\UserAuthenticate;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

/* Đây là một lớp PHP mở rộng HttpKernel và định nghĩa phần mềm trung gian và định tuyến phần mềm trung gian cho một
ứng dụng. */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    /* Mã này xác định một mảng phần mềm trung gian toàn cầu sẽ được chạy trong mọi yêu cầu tới
    ứng dụng. Các phần mềm trung gian này thực hiện các tác vụ khác nhau như xử lý các yêu cầu CORS, xác thực
    kích thước bài đăng, cắt chuỗi và chuyển đổi chuỗi rỗng thành null. */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    /* Thuộc tính `protected ` xác định các nhóm phần mềm trung gian có thể được áp dụng cho
    các tuyến đường trong ứng dụng. Trong mã này, có hai nhóm được xác định: `web` và `api`. */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            //\App\Http\Middleware\Localization::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    /* Mã này định nghĩa phần mềm trung gian định tuyến cho ứng dụng. Phần mềm trung gian định tuyến là phần mềm trung gian
    có thể được gán cho các tuyến hoặc nhóm tuyến cụ thể. Trong trường hợp này, phần mềm trung gian bao gồm
    phần mềm trung gian xác thực (`auth`, `auth.basic`, `auth.session`), phần mềm trung gian lưu trữ
    (`cache.headers`), phần mềm trung gian ủy quyền (`can`), phần mềm trung gian để chuyển hướng đã xác thực
    người dùng (`guest`), phần mềm trung gian để yêu cầu xác nhận mật khẩu (`password.confirm`), phần mềm trung gian
    để xác thực các URL đã ký (`signed`), phần mềm trung gian cho các yêu cầu giới hạn tốc độ (`throttle`),
    phần mềm trung gian để xác minh địa chỉ email (`đã xác minh`) và phần mềm trung gian tùy chỉnh để xác thực
    vai trò quản trị viên và người dùng (`admin` và `user`). */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'admin'=>AdminAuthenticate::class,
        'user'=>UserAuthenticate::class,
    ];
}
