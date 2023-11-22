<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// Quản trị xác thực.
/* Lớp AdminAuthenticate là một phần mềm trung gian kiểm tra xem người dùng có được xác thực và có quản trị viên hay không
đặc quyền trước khi cho phép truy cập vào các trang nhất định. */
class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    /**
     * Chức năng này kiểm tra xem người dùng có được xác thực và có quyền quản trị hay không và chuyển hướng họ
     * tương ứng với một thông báo cảnh báo.
     *
     * @param Yêu cầu yêu cầu Đối tượng yêu cầu HTTP chứa thông tin về thư đến
     * lời yêu cầu.
     * @param Đóng tiếp theo là một đóng đại diện cho phần mềm trung gian tiếp theo hoặc phần cuối cùng
     * đích của yêu cầu. Nó chịu trách nhiệm chuyển yêu cầu đến phần mềm trung gian tiếp theo hoặc
     * đích cuối cùng và trả lại phản hồi cho phần mềm trung gian trước đó.
     *
     * @return chuyển hướng đến trang đăng nhập với thông báo cảnh báo nếu người dùng không
     * đã xác thực hoặc chuyển hướng đến trang chủ kèm theo thông báo cảnh báo nếu người dùng đã xác thực
     * không phải là quản trị viên. Nếu người dùng được xác thực và là quản trị viên, chức năng sẽ cho phép yêu cầu
     * để chuyển sang hành động của bộ điều khiển hoặc phần mềm trung gian tiếp theo.
     */
    public function handle(Request $request, Closure $next)
    {
        if(!Auth::check()) {
            return redirect()->route('login')->with(['alert' => [
                'type' => 'warning',
                'title' => 'Từ chối truy cập!',
                'content' => 'Bạn không có quyền truy cập. Hãy đăng nhập tài khoản Admin để truy cập trang này.'
            ]]);
        } else if(!Auth::user()->admin) {
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'warning',
                'title' => 'Từ chối truy cập!',
                'content' => 'Tài khoản của bạn không có quyền truy cập. Trang này chỉ dành cho tài khoản Admin.'
            ]]);
        } else {
            return $next($request);
        }
    }
}
