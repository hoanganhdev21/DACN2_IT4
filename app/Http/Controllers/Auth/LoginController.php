<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Bộ điều khiển đăng nhập
    |------------------------------------------------- -------------------------
    |
    | Bộ điều khiển này xử lý xác thực người dùng cho ứng dụng và
    | chuyển hướng chúng đến màn hình chủ của bạn. Bộ điều khiển sử dụng một đặc điểm
    | để thuận tiện cung cấp chức năng của nó cho các ứng dụng của bạn.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
   /* `protected = '/';` là một thuộc tính trong lớp LoginController chỉ định URL
   để chuyển hướng người dùng đến sau khi họ đã đăng nhập thành công. Trong trường hợp này, nó được đặt thành
   URL gốc của ứng dụng. */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    /**
     * Chức năng này đặt phần mềm trung gian cho người dùng khách, ngoại trừ chức năng đăng xuất.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    /**
     *Đây là một hàm PHP xác thực các trường email và mật khẩu trong biểu mẫu đăng nhập.
     *
     * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu chứa dữ liệu được gửi
     * bởi máy khách trong yêu cầu HTTP.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    /**
     * Chức năng này kiểm tra xem người dùng đang hoạt động hay quản trị viên sau khi xác thực và chuyển hướng họ
     * tương ứng với một thông báo cảnh báo.
     *
     * @param Yêu cầu yêu cầu Đối tượng yêu cầu HTTP chứa thông tin về thư đến
     * lời yêu cầu.
     * Người dùng @param Đối tượng người dùng đã xác thực được trả về sau khi xác thực thành công.
     *
     * @return phản hồi chuyển hướng đến bảng điều khiển quản trị viên hoặc trang chủ với thông báo cảnh báo
     * tùy thuộc vào trạng thái của người dùng (hoạt động hoặc quản trị viên). Nếu người dùng không hoạt động, chức năng sẽ ghi lại
     * chúng ra ngoài và trả lại phản hồi chuyển hướng đến trang trước với thông báo cảnh báo.
     */
    /**
     * Chức năng này kiểm tra xem người dùng đang hoạt động hay quản trị viên sau khi xác thực và chuyển hướng họ
     * tương ứng với một thông báo cảnh báo.
     *
     * @param Yêu cầu yêu cầu Đối tượng yêu cầu HTTP chứa thông tin về thư đến
     * lời yêu cầu.
     * Người dùng @param Đối tượng người dùng đã xác thực được trả về sau khi xác thực thành công.
     *
     * @return phản hồi chuyển hướng đến bảng điều khiển quản trị viên hoặc trang chủ, tùy thuộc vào
     * vai trò của người dùng. Nếu người dùng không hoạt động, chức năng sẽ đăng xuất họ và trả về một chuyển hướng
     * trả lời trang trước bằng một thông báo cảnh báo.
     */
    protected function authenticated(Request $request, $user)
    {
        if(!$user->active) {
            auth()->logout();
            return back()->withInput()->with(['alert' => [
                'type' => 'warning',
                'title' => 'Tài khoản chưa được kích hoạt!',
                'content' => 'Hãy kiểm tra email để kích hoạt tài khoản.'
            ]]);
        } else if($user->admin) {
            return redirect()->route('admin.dashboard')->with(['alert' => [
                'type' => 'success',
                'title' => 'Đăng nhập thành công',
                'content' => 'Chào mừng bạn đến với trang quản trị website PhoneStore'
            ]]);
        } else {
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'success',
                'title' => 'Đăng nhập thành công',
                'content' => 'Chào mừng bạn đến với website PhoneStore của chúng tôi'
            ]]);
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
   
    /**
     * Chức năng này chuyển hướng người dùng trở lại trang trước với thông báo lỗi nếu đăng nhập
     * nỗ lực không thành công.
     *
     * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu đại diện cho một HTTP
     * lời yêu cầu. Nó chứa thông tin về yêu cầu như phương thức HTTP, tiêu đề và
     * thông số. Trong trường hợp này, nó được sử dụng để truy xuất URL trước đó để người dùng có thể
     * chuyển hướng trở lại trang đăng nhập với đầu vào của họ và một
     *
     * @return Phản hồi chuyển hướng với dữ liệu đầu vào và thông báo cho biết rằng nỗ lực đăng nhập
     * thất bại.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()->withInput()->with('message', trans('auth.failed'));
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    
    /**
     * Chức năng chuyển hướng người dùng đến trang chủ với thông báo cảnh báo thành công sau khi đăng xuất.
     *
     * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu đại diện cho một HTTP
     * lời yêu cầu. Nó chứa thông tin về yêu cầu như phương thức HTTP, tiêu đề và
     * thông số. Trong ngữ cảnh này, nó được sử dụng để chuyển hướng người dùng đến trang chủ sau khi họ có
     * đã đăng xuất và chuyển thông báo cảnh báo thành công tới
     *
     * @return Chuyển hướng đến tuyến "home_page" với thông báo cảnh báo thành công có chứa tiêu đề và
     * nội dung.
     */
    protected function loggedOut(Request $request)
    {
        return redirect()->route('home_page')->with(['alert' => [
            'type' => 'success',
            'title' => 'Đăng xuất thành công',
            'content' => 'Chúc bạn một ngày vui vẻ.'
        ]]);
    }
}
