<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\ActiveAccountNotification;

/* Lớp `Người dùng` là một mô hình trong ứng dụng PHP đại diện cho người dùng và định nghĩa các thuộc tính của nó,
mối quan hệ với các mô hình khác và phương thức gửi đặt lại mật khẩu và kích hoạt tài khoản
thông báo. */
class User extends Authenticatable
{
    /* `use HasApiTokens, HasFactory, Notifiable;` đang nhập và sử dụng ba đặc điểm khác nhau trong
    Lớp mô hình `Người dùng`. */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    
/* `protected ` là một mảng xác định thuộc tính nào của mô hình `User` có thể được
    khối lượng được giao. Trong trường hợp này, nó cho phép `tên`, `email`, `điện thoại`, `mật khẩu` và
    Các thuộc tính `active_token` sẽ được đặt trong một câu lệnh gán, chẳng hạn như khi tạo một
    người dùng mới hoặc cập nhật nhiều người dùng cùng một lúc. Bất kỳ thuộc tính nào khác không được liệt kê trong `` sẽ
    bị bỏ qua trong quá trình chuyển nhượng hàng loạt vì lý do bảo mật. */
    protected $fillable = [
        'name', 'email', 'phone', 'password', 'active_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
   /* Thuộc tính `protected ` được sử dụng để chỉ định thuộc tính nào sẽ bị ẩn khi
   mô hình được tuần tự hóa thành một mảng hoặc JSON. Trong trường hợp này, `password`, `remember_token`, và
   Các thuộc tính `active_token` bị ẩn vì lý do bảo mật. Điều này có nghĩa là khi mô hình
   được chuyển đổi thành một mảng hoặc JSON, các thuộc tính này sẽ không được đưa vào đầu ra. */
    protected $hidden = [
        'password', 'remember_token', 'active_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    /* `protected ` được sử dụng để xác định kiểu dữ liệu của các thuộc tính cụ thể của mô hình. trong này
    trường hợp, `email_verified_at` được chuyển thành loại `datetime`, có nghĩa là khi thuộc tính này
    được truy xuất từ ​​cơ sở dữ liệu, nó sẽ được trả về dưới dạng một đối tượng `DateTime` thay vì một chuỗi.
    Điều này cho phép thao tác và định dạng các giá trị ngày và giờ dễ dàng hơn. */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

   /**
    * Hàm trả về nhiều quan hệ cho một model trong ứng dụng PHP Laravel.
    *
    * @return Đoạn mã này đang định nghĩa một số phương thức trong một lớp PHP để trả về các mối quan hệ cho các lớp khác
    * mô hình trong một ứng dụng Laravel. Cụ thể, các phương thức đang trả về mối quan hệ "hasMany"
    * cho các mô hình sau: Nhận xét, Thông báo, Đặt hàng, Đăng và Bình chọn sản phẩm.
    */
    public function comments() {
        return $this->hasMany('App\Models\Comment');
    }
    public function notices() {
        return $this->hasMany('App\Models\Notice');
    }
    public function orders() {
        return $this->hasMany('App\Models\Order');
    }
    public function posts() {
        return $this->hasMany('App\Models\Post');
    }
    public function product_votes() {
        return $this->hasMany('App\Models\ProductVote');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    
/**
     * Hàm này gửi thông báo đặt lại mật khẩu bằng lớp ResetPasswordNotification.
     *
     * Mã thông báo @param Tham số là một chuỗi duy nhất được tạo khi người dùng yêu cầu
     * đặt lại mật khẩu của họ. Mã thông báo này được sử dụng để xác minh danh tính của người dùng và cho phép họ đặt lại
     * mật khẩu của họ một cách an toàn. Mã thông báo thường được gửi đến địa chỉ email hoặc số điện thoại của người dùng.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the active account notification.
     *
     * @param  string  $token
     * @return void
     */
   /**
    * Chức năng này gửi thông báo tài khoản đang hoạt động kèm theo token.
    *
    * Mã thông báo @param Tham số mã thông báo là một chuỗi duy nhất được tạo và sử dụng để xác minh
    * kích hoạt tài khoản của người dùng. Nó thường được gửi đến địa chỉ email của người dùng và được sử dụng để xác nhận
    * rằng người dùng có quyền truy cập vào địa chỉ email được cung cấp trong quá trình đăng ký.
    */
    public function sendActiveAccountNotification($token)
    {
        $this->notify(new ActiveAccountNotification($token));
    }
}
