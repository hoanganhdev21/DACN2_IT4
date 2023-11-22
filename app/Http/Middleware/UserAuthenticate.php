<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/* Lớp UserAuthenticate xử lý các yêu cầu gửi đến và kiểm tra xem người dùng có được xác thực hay không và
được phép truy cập trang được yêu cầu. */
class UserAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Auth::check()) {
            return redirect()->route('login')->with(['alert' => [
                'type' => 'warning',
                'title' => 'Từ chối truy cập!',
                'content' => 'Bạn không có quyền truy cập. Hãy đăng nhập để truy cập trang này.'
            ]]);
        } else if(Auth::user()->admin) {
            return redirect()->route('admin.dashboard');
        } else {
            return $next($request);
        }
    }
}
