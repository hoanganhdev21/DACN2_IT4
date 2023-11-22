<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Bộ điều khiển đặt lại mật khẩu
    |------------------------------------------------- -------------------------
    |
    | Bộ điều khiển này chịu trách nhiệm xử lý email đặt lại mật khẩu và
    | bao gồm một đặc điểm hỗ trợ gửi các thông báo này từ
    | ứng dụng của bạn cho người dùng của bạn. Hãy khám phá đặc điểm này.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Tạo một thể hiện bộ điều khiển mới.
     *
     * @return vô hiệu
     */
   /**
    * Đây là hàm tạo cho lớp PHP đặt phần mềm trung gian thành 'khách'.
    */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    /**
     * Hàm này trả về một mảng thông tin đăng nhập dựa trên email và trạng thái hoạt động được cung cấp trong
     * yêu cầu.
     * 
     * @param Request yêu cầu là một thể hiện của lớp Yêu cầu đại diện cho một HTTP
     * lời yêu cầu. Nó chứa thông tin về yêu cầu như phương thức HTTP, tiêu đề và
     * thông số. Trong trường hợp này, nó đang được sử dụng để truy xuất tham số email từ yêu cầu
     * 
     * @return Một mảng có email và trạng thái hoạt động của người dùng. Chức năng này có thể được sử dụng để
     * truy xuất thông tin đăng nhập của người dùng cho mục đích xác thực.
     */
    protected function credentials(Request $request)
    {
        return array('email' => $request->email, 'active' => true);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    /**
     * This function redirects the user to the home page with a success message after successfully
     * sending a password reset email.
     * 
     * @param Request request  is an instance of the Illuminate\Http\Request class, which
     * represents an HTTP request. It contains information about the request such as the HTTP method,
     * headers, and input data. In this function, it is used to retrieve data from the request and pass
     * it to the redirect response.
     * @param response  is the response returned by the sendResetLinkEmail method in the
     * PasswordController. It indicates whether the password reset link was successfully sent or not.
     * It can be a string or an instance of the Illuminate\Http\JsonResponse class.
     * 
     * @return A redirect response to the "home_page" route with a session alert message containing
     * information about the success of sending a password reset email.
     */

    /**
     * Chức năng này chuyển hướng người dùng đến trang chủ với thông báo thành công sau khi thành công
     * gửi email đặt lại mật khẩu.
     * 
     * @param Yêu cầu yêu cầu là một thể hiện của lớp Yêu cầu, đại diện cho một HTTP
     * yêu cầu được thực hiện cho ứng dụng. Nó chứa thông tin về yêu cầu, chẳng hạn như HTTP
     *, URL, tiêu đề và bất kỳ dữ liệu nào được gửi trong phần thân yêu cầu. Trong chức năng này, các
     * tham số được sử dụng để lấy dữ liệu từ
     * phản hồi @param là phản hồi được trả về bởi phương thức sendResetLinkEmail trong
     * Bộ điều khiển mật khẩu. Nó cho biết liệu liên kết đặt lại mật khẩu có được gửi thành công hay không.
     * Nó có thể là một chuỗi hoặc một thể hiện của lớp Illuminate\Http\JsonResponse.
     * 
     * @return Phản hồi chuyển hướng tới tuyến "home_page" với thông báo cảnh báo phiên có chứa
     * thông tin về sự thành công của việc gửi email đặt lại mật khẩu.
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return redirect()->route('home_page')->with(['alert' => [
            'type' => 'info',
            'title' => 'Thông báo',
            'content' => 'Gửi email cấp lại mật khẩu thành công. Vui lòng kiểm tra email.'
        ]]);
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    /**
     * This function redirects the user back to the previous page with an error message if sending a
     * password reset link fails.
     * 
     * @param Request request  is an instance of the Illuminate\Http\Request class, which
     * represents an incoming HTTP request. It contains information about the request such as the HTTP
     * method, headers, and any data sent in the request body. In this context, it is used to retrieve
     * input data from the previous request and pass it back
     * @param response The `` parameter is the response returned by the password broker when
     * attempting to send a password reset link. It can be a string or an instance of
     * `Illuminate\Http\JsonResponse`.
     * 
     * @return A redirect response with input data and a message indicating that the attempt to send a
     * password reset link has failed.
     */


   /**
    * Chức năng này chuyển hướng người dùng trở lại trang trước với thông báo lỗi nếu gửi
    * liên kết đặt lại mật khẩu không thành công.
    * 
    * @param Yêu cầu yêu cầu là một thể hiện của lớp Illuminate\Http\Request mà
    * đại diện cho một yêu cầu HTTP đến. Nó chứa thông tin về yêu cầu như HTTP
    *, tiêu đề và bất kỳ dữ liệu nào được gửi trong phần thân yêu cầu. Trong bối cảnh này, nó được sử dụng để truy xuất
    * nhập dữ liệu từ yêu cầu trước đó và chuyển lại cho
    *phản hồi @param Tham số `` là phản hồi được trả về bởi trình môi giới mật khẩu khi
    * cố gắng gửi liên kết đặt lại mật khẩu. Nó có thể là một chuỗi hoặc một thể hiện của
    * `Chiếu sáng\Http\JsonResponse`.
    * 
    * @return Phản hồi chuyển hướng với dữ liệu đầu vào và thông báo cho biết nỗ lực gửi
    * liên kết đặt lại mật khẩu không thành công.
    */
    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return redirect()->back()->withInput()->with('message', trans('passwords.failed'));
    }
}
