<?php

namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Advertise;

/* Lớp UserController xử lý xác thực người dùng và cho phép người dùng xem và chỉnh sửa tài khoản của họ
thông tin. */
class UserController extends Controller
{
  /**
   * Chức năng kiểm tra xem người dùng có được xác thực hay không và chuyển hướng họ đến trang thích hợp,
   * hiển thị thông báo cảnh báo nếu cần.
   *
   * @return chuyển hướng đến bảng điều khiển quản trị viên kèm theo thông báo cảnh báo nếu người dùng được xác thực
   * là quản trị viên hoặc chế độ xem có tên "pages.show_user" với dữ liệu chứa thông tin về
   * người dùng được xác thực và danh sách tối đa 5 quảng cáo đang hoạt động không có trên trang chủ. Nếu như
   * người dùng chưa được xác thực, hàm trả về chuyển hướng đến trang đăng nhập kèm theo cảnh báo
   * tin nhắn.
   */
  public function show()
  {
    if(Auth::check()) {
      if (Auth::user()->admin) {
        return redirect()->route('admin.dashboard')->with(['alert' => [
          'type' => 'warning',
          'title' => 'Cảnh Báo',
          'content' => 'Bạn không có quyền truy cập vào trang này!'
        ]]);
      } else {
        $advertises = Advertise::where([
          ['start_date', '<=', date('Y-m-d')],
          ['end_date', '>=', date('Y-m-d')],
          ['at_home_page', '=', false]
        ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

        $user = User::select('id', 'name', 'email', 'phone', 'address', 'avatar_image')
          ->where('id', Auth::user()->id)->first();

        return view('pages.show_user')->with('data',['user' => $user, 'advertises' => $advertises]);
      }
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
      ]]);
    }
  }

  /**
   * Chức năng kiểm tra xem người dùng đã đăng nhập chưa và chuyển hướng họ đến trang thích hợp,
   * hiển thị thông tin liên quan như chi tiết người dùng và quảng cáo.
   *
   * @return chế độ xem có tên 'pages.edit_user' với dữ liệu chứa thông tin của người dùng và danh sách
   * quảng cáo. Nếu người dùng chưa đăng nhập, nó sẽ chuyển hướng họ đến trang đăng nhập kèm theo cảnh báo
   * tin nhắn. Nếu người dùng là quản trị viên, nó sẽ chuyển hướng họ đến bảng điều khiển dành cho quản trị viên kèm theo thông báo cảnh báo.
   */
  public function edit()
  {
    if(Auth::check()) {
      if (Auth::user()->admin) {
        return redirect()->route('admin.dashboard')->with(['alert' => [
          'type' => 'warning',
          'title' => 'Cảnh Báo',
          'content' => 'Bạn không có quyền truy cập vào trang này!'
        ]]);
      } else {
        $advertises = Advertise::where([
          ['start_date', '<=', date('Y-m-d')],
          ['end_date', '>=', date('Y-m-d')],
          ['at_home_page', '=', false]
        ])->latest()->limit(5)->get(['product_id', 'title', 'image']);

        $user = User::select('id', 'name', 'email', 'phone', 'address', 'avatar_image')
          ->where('id', Auth::user()->id)->first();

        return view('pages.edit_user')->with('data',['user' => $user, 'advertises' => $advertises]);
      }
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
      ]]);
    }
  }

  /**
   * Chức năng này lưu thông tin người dùng và hiển thị các cảnh báo thích hợp dựa trên người dùng
   * trạng thái xác thực và xác thực đầu vào.
   *
   * @param Yêu cầu yêu cầu là một thể hiện của lớp Illuminate\Http\Request, mà
   * đại diện cho một yêu cầu HTTP. Nó chứa thông tin về yêu cầu như phương thức HTTP,
   * URL, tiêu đề và bất kỳ dữ liệu nào được gửi cùng với yêu cầu. Trong chức năng này, được sử dụng để
   * lấy dữ liệu đầu vào từ biểu mẫu được gửi bởi
   *
   * @return phản hồi chuyển hướng đến các tuyến khác nhau với các thông báo cảnh báo dựa trên các điều kiện nhất định.
   * Nếu người dùng chưa đăng nhập, nó sẽ chuyển hướng đến trang đăng nhập với thông báo cảnh báo. Nếu người dùng
   * là quản trị viên, nó chuyển hướng đến bảng điều khiển quản trị viên với thông báo cảnh báo. Nếu người dùng không phải là một
   * quản trị viên và ID người dùng trong yêu cầu khớp với ID người dùng đã xác thực, nó xác thực yêu cầu
   * dữ liệu
   */
  public function save(Request $request)
  {
    if(Auth::check()) {
      if (Auth::user()->admin) {
        return redirect()->route('admin.dashboard')->with(['alert' => [
          'type' => 'warning',
          'title' => 'Cảnh Báo',
          'content' => 'Bạn không có quyền thực hiện chức năng này!'
        ]]);
      } elseif(Auth::user()->id != $request->user_id) {
        return back()->with(['alert' => [
          'type' => 'info',
          'title' => 'Thông Báo',
          'content' => 'Đã xẩy ra lỗi trong quá trình cập nhật thông tin. Vui lòng nhập lại!'
        ]]);
      } else {
        if($request->phone != Auth::user()->phone) {
          $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'phone' => 'required|string|size:10|regex:/^0[^6421][0-9]{8}$/|unique:users',
            'address' => 'required',
          ], [
            'name.required' => 'Tên không được để trống!',
            'name.string' => 'Tên phải là một chuỗi ký tự!',
            'name.max' => 'Tên không được vượt quá :max kí tự!',
            'phone.required' => 'Số điện thoại không được để trống!',
            'phone.string' => 'Số điện thoại phải là một chuỗi ký tự!',
            'phone.size' => 'Số điện thoại phải có độ dài :size chữ số!',
            'phone.regex' => 'Số điện thoại không hợp lệ!',
            'phone.unique' => 'Số điện thoại đã tồn tại!',
            'address.required' => 'Địa chỉ không được để trống!',
          ]);
        } else {
          $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'address' => 'required',
          ], [
            'name.required' => 'Tên không được để trống!',
            'name.string' => 'Tên phải là một chuỗi ký tự!',
            'name.max' => 'Tên không được vượt quá :max kí tự!',
            'address.required' => 'Địa chỉ không được để trống!',
          ]);
        }

        if ($validator->fails()) {
          return back()
            ->withErrors($validator)
            ->withInput();
        }

        $user = User::where('id', $request->user_id)->first();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if($request->hasFile('avatar_image')){
          $image = $request->file('avatar_image');
          $image_name = time().'_'.$image->getClientOriginalName();
          $image->storeAs('images/avatars',$image_name,'public');

          if($user->avatar_image != NULL) {
            Storage::disk('public')->delete('images/avatars/'.$user->avatar_image);
          }

          $user->avatar_image = $image_name;
        }

        $user->save();

        return redirect()->route('show_user')->with(['alert' => [
          'type' => 'success',
          'title' => 'Thành Công',
          'content' => 'Cập nhật thông tin tài khoản thành công.'
        ]]);
      }
    } else {
      return redirect()->route('login')->with(['alert' => [
        'type' => 'warning',
        'title' => 'Cảnh Báo',
        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
      ]]);
    }
  }
}
