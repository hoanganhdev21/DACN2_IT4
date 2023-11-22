<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Đăng ký bộ điều khiển
    |------------------------------------------------- -------------------------
    |
    | Bộ điều khiển này xử lý việc đăng ký người dùng mới cũng như
    | xác nhận và tạo. Theo mặc định, bộ điều khiển này sử dụng một đặc điểm để
    | cung cấp chức năng này mà không yêu cầu bất kỳ mã bổ sung nào.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    /* `protected = '/';` là một thuộc tính trong lớp `RegisterController` chỉ định
    URL để chuyển hướng người dùng đến sau khi họ đã đăng ký thành công. Trong trường hợp này, nó được đặt
    đến URL gốc `/`, có nghĩa là người dùng sẽ được chuyển hướng đến trang chủ của
    trang web sau khi họ đã đăng ký. Thuộc tính này có thể được ghi đè trong các lớp con nếu một
    URL chuyển hướng khác là mong muốn. */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    /**
     * Đây là chức năng xây dựng đặt phần mềm trung gian thành 'khách'.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    
    /**
     * Đây là một hàm xác thực trong PHP để kiểm tra xem dữ liệu đầu vào có đáp ứng các yêu cầu nhất định cho
     * tên, email, số điện thoại và mật khẩu.
     *
     * Dữ liệu mảng @param Tham số là một mảng chứa dữ liệu đầu vào cần được
     * xác thực. Nó thường bao gồm tên, email, số điện thoại và mật khẩu của người dùng.
     *
     * @return Một đối tượng trình xác thực đang được trả về. Đối tượng trình xác thực được tạo bằng Laravel
     * Lớp trình xác thực và nó được sử dụng để xác thực dữ liệu được truyền dưới dạng đối số cho hàm. Các
     * quy tắc xác thực được xác định bằng cách sử dụng một loạt quy tắc cho từng trường.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'size:10', 'regex:/^0[^6421][0-9]{8}$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    
    /**
     * Chức năng này tạo người dùng mới với dữ liệu đã cho và tạo mã thông báo hoạt động cho người dùng.
     *
     * Dữ liệu mảng @param là một mảng chứa thông tin của người dùng như tên, email,
     * điện thoại và mật khẩu. Nó được truyền dưới dạng tham số cho hàm create().
     *
     * @return Phương thức `create()` đang trả về một thể hiện `Người dùng` mới được tạo bằng
     * cung cấp `` mảng. Mảng `` chứa tên, email, điện thoại và mật khẩu của người dùng.
     * Mật khẩu được băm bằng phương thức `Hash::make()` trước khi được lưu trữ trong cơ sở dữ liệu.
     * Ngoài ra, một chuỗi ngẫu nhiên gồm 40 ký tự được tạo và lưu trữ dưới dạng `hoạt động của người dùng
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'active_token' => str::random(40),
        ]);
    }

    /**
     * Chức năng kích hoạt kiểm tra xem mã thông báo kích hoạt của người dùng có hợp lệ hay không và kích hoạt tài khoản của họ
     * nếu nó chưa được kích hoạt.
     *
     * Mã thông báo @param Mã thông báo kích hoạt được sử dụng để xác minh địa chỉ email của người dùng và kích hoạt
     * tài khoản của họ.
     *
     * @return Chức năng này trả về phản hồi chuyển hướng đến trang chủ với thông báo cảnh báo. Các
     * nội dung của thông báo cảnh báo phụ thuộc vào kết quả của quá trình kích hoạt. Nếu kích hoạt
     * thành công, một thông báo thành công được trả về. Nếu tài khoản đã được kích hoạt, một cảnh báo
     * tin nhắn được trả lại. Nếu mã thông báo kích hoạt không hợp lệ, một thông báo lỗi sẽ được trả về.
     */
    public function activation($token) {
        $user = User::where('active_token', $token)->first();
        if(isset($user)) {
            if(!$user->active) {
                $user->active = 1;
                $user->save();
                return redirect()->route('home_page')->with(['alert' => [
                    'type' => 'success',
                    'title' => 'Kích hoạt tài khoản thành công',
                    'content' => 'Chúc mừng bạn đã kích hoạt tài khoản thành công. Bạn có thể đẳng nhập ngay bây giờ.'
                ]]);
            }
            else {
                return redirect()->route('home_page')->with(['alert' => [
                    'type' => 'warning',
                    'title' => 'Tài khoản đã được kích hoạt',
                    'content' => 'Tài khoản đã được kích hoạt từ trước. Bạn có thể đẳng nhập ngày bây giờ.'
                ]]);
            }
        } else {
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'error',
                'title' => 'Kích hoạt tài khoản không thành công',
                'content' => 'Mã kích hoạt không đúng. vui lòng kiểm tra lại email đăng ký!'
            ]]);
        }
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    
   /**
    * Chức năng này gửi thông báo đến email của người dùng và đăng xuất họ sau khi thành công
    * sự đăng ký.
    *
    * @param Yêu cầu yêu cầu là một thể hiện của lớp Illuminate\Http\Request, mà
    * đại diện cho một yêu cầu HTTP. Nó chứa thông tin về yêu cầu như phương thức HTTP,
    * tiêu đề và dữ liệu đầu vào. Trong ngữ cảnh này, nó được sử dụng để lấy dữ liệu từ mẫu đăng ký
    * được gửi bởi người dùng.
    * Người dùng @param là một thể hiện của mô hình Người dùng đại diện cho người dùng mới đăng ký.
    *
    * @return chuyển hướng đến trang chủ với thông báo cảnh báo thành công. Tin nhắn thông báo cho người dùng
    * rằng đăng ký tài khoản của họ đã thành công và yêu cầu họ kiểm tra email để kích hoạt
    * tài khoản của họ.
    */
    protected function registered(Request $request, $user)
    {
        $user->sendActiveAccountNotification($user->active_token);

        $this->guard()->logout();

        return redirect()->route('home_page')->with(['alert' => [
            'type' => 'success',
            'title' => 'Đăng ký tài khoản thành công',
            'content' => 'Vui lòng kiểm tra email đăng ký để kích hoạt tài khoản.'
        ]]);
    }
}
