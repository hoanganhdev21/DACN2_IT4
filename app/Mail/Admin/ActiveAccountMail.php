<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/* Đây là lớp PHP để gửi email kích hoạt tài khoản, với hàm tạo để đặt dữ liệu và
một phương thức xây dựng để tạo thông điệp email. */
class ActiveAccountMail extends Mailable
{
   /* `use Queueable, SerializesModels;` đang nhập hai đặc điểm `Queueable` và `SerializesModels`
   vào lớp `ActiveAccountMail`. Những đặc điểm này cung cấp chức năng bổ sung cho lớp. */
    use Queueable, SerializesModels;

    /**
     * Data Mail
     *
     * @var array
     */
   /* `public ;` đang khai báo thuộc tính công cộng có tên `` cho lớp `ActiveAccountMail`.
   Thuộc tính này có thể được truy cập từ bên ngoài lớp và được sử dụng để lưu trữ dữ liệu sẽ được
   được chuyển đến chế độ xem email. Trong trường hợp này, dữ liệu được truyền qua hàm tạo và sẽ được
   được sử dụng để điền vào nội dung email. */
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    /**
     * Đây là một hàm tạo trong PHP đặt giá trị của thuộc tính "dữ liệu" của một đối tượng.
     *
     * Dữ liệu @param Tham số `` đang được truyền cho hàm tạo của một lớp. Nó là một
     * biến chứa một số dữ liệu sẽ được lớp sử dụng. Hàm tạo gán giá trị
     * của `` đối với thuộc tính `->data` của lớp.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    /**
     * Hàm PHP này trả về chế độ xem đánh dấu cho mẫu email để kích hoạt tài khoản quản trị viên.
     *
     * @return Hàm `build()` đang trả về kết quả hiển thị tệp dạng xem Markdown được định vị
     * tại `emails.admin.active_account`.
     */
    public function build()
    {
        return $this->markdown('emails.admin.active_account');
    }
}
