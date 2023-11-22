<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * Một danh sách các đầu vào không bao giờ được đưa vào phiên đối với các ngoại lệ xác thực.
     *
     * @var array<int, string>
     */
    /* `protected ` là một mảng chứa tên của các trường đầu vào không được đưa vào phiên khi có ngoại lệ xác thực. Điều này có nghĩa là nếu có lỗi xác thực trên một trong các trường này, thì giá trị của trường đó sẽ không được lưu trữ trong phiên và sẽ không có sẵn trong yêu cầu tiếp theo. Đây là một biện pháp bảo mật để ngăn thông tin nhạy cảm như mật khẩu được lưu trữ trong phiên. Trong trường hợp này, các trường 'current_password', 'password' và 'password_confirmation' được chỉ định là các trường không được đưa vào phiên. */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];


    
    /**
     * Đăng ký các cuộc gọi lại xử lý ngoại lệ cho ứng dụng.
     *
     * @return void
     */
    /**
     * Đây là một hàm PHP đăng ký trình xử lý lỗi có thể báo cáo cho các trường hợp ngoại lệ.
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    
}
