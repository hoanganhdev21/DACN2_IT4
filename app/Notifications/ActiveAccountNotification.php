<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/* Lớp ActiveAccountNotification là một lớp PHP mở rộng lớp Thông báo và được sử dụng
để gửi email thông báo kích hoạt tài khoản. */
class ActiveAccountNotification extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    
/* `public ;` đang khai báo thuộc tính public có tên `` cho
    lớp `ActiveAccountNotification`. Thuộc tính này được sử dụng để lưu trữ mã thông báo đặt lại mật khẩu
    sẽ được gửi trong email thông báo. Nó có thể được truy cập và sửa đổi từ bên ngoài lớp. */
    public $token;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    /* `public static ;` đang khai báo một thuộc tính public static có tên ``
    cho lớp `ActiveAccountNotification`. Thuộc tính này được sử dụng để lưu trữ chức năng gọi lại
    sẽ được sử dụng để xây dựng thư cho thông báo. Nó có thể được truy cập và
    sửa đổi từ bên ngoài lớp học. Từ khóa `static` được sử dụng để làm cho thuộc tính có thể truy cập được
    mà không cần tạo một thể hiện của lớp. */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    /**
     * Đây là hàm tạo trong PHP đặt giá trị của thuộc tính lớp được gọi là "mã thông báo".
     *
     * Mã thông báo @param Tham số `` là một biến được truyền cho hàm tạo của một lớp.
     * Nó được sử dụng để đặt giá trị của thuộc tính `` của lớp. Mục đích của việc này
     * hàm tạo là khởi tạo đối tượng với giá trị mã thông báo được cung cấp.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
   /**
    * Hàm này trả về một mảng chứa chuỗi "mail".
    *
    * @param notifiable Tham số là một thể hiện của một lớp đại diện cho thực thể
    * sẽ nhận được thông báo. Đó có thể là người dùng, khách hàng hoặc bất kỳ đối tượng nào khác
    * cần được thông báo. Phương thức via() được sử dụng để chỉ định các kênh mà thông qua đó
    * thông báo nên được gửi đến không
    *
    * @return Một mảng chứa chuỗi "mail" đang được trả về.
    */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    /**
     * Chức năng này tạo một email có liên kết để kích hoạt tài khoản trên một trang web.
     *
     * @param notifiable Tham số notifiable là một thể hiện của lớp sẽ nhận
     * thông báo. Nó có thể là người dùng, khách hàng hoặc bất kỳ mô hình nào khác sử dụng đặc điểm Notifiable.
     *
     * @return Hàm `toMail` đang trả về một đối tượng `MailMessage` với lời chào, chủ đề,
     * nội dung thư và nút hành động để kích hoạt tài khoản. Nếu `toMailCallback` được đặt, nó
     * sẽ gọi hàm đó thay vào đó và chuyển vào các tham số `` và `->token`.
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->greeting('Hello '.$notifiable->name.'.')
            ->subject('Active Account Notification')
            ->line('Email này đã được liên kết với một tài khoản trên hệ thống website. Vui lòng click vào nút bên dưới để kích hoạt.')
            ->action('Active Account', route('active_account', ['token' => $this->token]));
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    /**
     * Chức năng này đặt chức năng gọi lại được sử dụng để gửi email.
     *
     * Gọi lại @param Tham số là một hàm hoặc phương thức sẽ được sử dụng để tùy chỉnh
     * thông điệp email sẽ được gửi. Nó sẽ được gọi khi phương thức toMail() được gọi trên một
     * thể hiện của lớp định nghĩa hàm tĩnh này. Các chức năng nên chấp nhận một
     * Ví dụ về Illuminate\Mail\Mailable
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
