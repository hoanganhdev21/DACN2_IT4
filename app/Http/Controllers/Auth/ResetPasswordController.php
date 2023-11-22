<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    |Bộ điều khiển đặt lại mật khẩu
    |------------------------------------------------- -------------------------
    |
    | Bộ điều khiển này chịu trách nhiệm xử lý các yêu cầu đặt lại mật khẩu
    | và sử dụng một đặc điểm đơn giản để bao gồm hành vi này. Bạn được tự do
    | khám phá đặc điểm này và ghi đè bất kỳ phương pháp nào bạn muốn điều chỉnh.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    /*`protected = '/';` đang đặt URL mà người dùng sẽ được chuyển hướng đến sau
    đặt lại thành công mật khẩu của họ. Trong trường hợp này, nó được đặt thành URL gốc của
    ứng dụng.*/
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
   /**
   * Đây là hàm tạo cho lớp PHP đặt phần mềm trung gian thành 'khách'.
    */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    
   /**
    * Chức năng này đăng xuất người dùng và chuyển hướng họ về trang chủ với thông báo thành công sau
    * thay đổi thành công mật khẩu của họ.
    *
    * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu chứa HTTP
    * yêu cầu thông tin như tiêu đề, tham số và cookie.
    * Phản hồi @param là phản hồi được trả về bởi bộ điều khiển đặt lại mật khẩu sau một
    * thiết lập lại mật khẩu thành công. Nó chứa thông tin về sự thành công hay thất bại của
    * nỗ lực thiết lập lại mật khẩu.
    *
    * @return chuyển hướng đến trang chủ với thông báo cảnh báo thành công cho biết rằng mật khẩu đã
    * đã được thay đổi thành công và người dùng có thể đăng nhập vào hệ thống.
    */
    protected function sendResetResponse(Request $request, $response)
    {
        $this->guard()->logout();
        return redirect()->route('home_page')->with(['alert' => [
            'type' => 'success',
            'title' => 'Thay đổi mật khẩu thành công',
            'content' => 'Mật khẩu đã được thay đổi. Bạn có thể đăng nhập vào hệ thống ngay bây giờ.'
        ]]);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */

   /**
    * Chức năng này chuyển hướng người dùng trở lại trang trước bằng một thông báo nếu đặt lại mật khẩu
    * thất bại.
    *
    * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu đại diện cho một HTTP
    * yêu cầu được thực hiện cho ứng dụng. Nó chứa thông tin về yêu cầu như HTTP
    *, URL, tiêu đề và bất kỳ dữ liệu nào được gửi trong phần thân yêu cầu. Trong chức năng này, các
    * tham số được sử dụng để chuyển hướng người dùng trở lại
    * Phản hồi @param là một tham số chuỗi đại diện cho thông báo lỗi được trả về bởi
    * quá trình thiết lập lại mật khẩu. Nó có thể là một trong những điều sau đây: mật khẩu, mã thông báo, người dùng hoặc điều chỉnh.
    *
    * @return Phản hồi chuyển hướng với thông báo được hiển thị trong phiên.
    */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()->with('message', trans($response));
    }
}
