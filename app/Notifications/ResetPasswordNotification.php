<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/* Đây là lớp PHP để gửi thông báo đặt lại mật khẩu qua email. */
class ResetPasswordNotification extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    /* `public ;` đang khai báo một thuộc tính public có tên là `` cho
    lớp `ResetPasswordNotification`. Thuộc tính này được sử dụng để lưu trữ mã thông báo đặt lại mật khẩu
    sẽ được gửi đến người dùng qua email. */
    public $token;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
   /* `public static ;` đang khai báo một thuộc tính public static được gọi là ``
   cho lớp `ResetPasswordNotification`. Thuộc tính này được sử dụng để lưu trữ chức năng gọi lại
   có thể được sử dụng để tùy chỉnh thông báo email được gửi tới người dùng để đặt lại
   mật khẩu. Phương thức `toMail` của lớp `ResetPasswordNotification` kiểm tra xem thuộc tính này có
   set và nếu vậy, nó gọi hàm gọi lại để tạo thư. Điều này cho phép lớn hơn
   linh hoạt trong việc tùy chỉnh thông báo email cho thông báo đặt lại mật khẩu. */
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
     * Chức năng này tạo thông báo email đặt lại mật khẩu cho người dùng.
     *
     * @param notifiable Tham số notifiable là một thể hiện của lớp đại diện cho
     * thực thể nhận thông báo, chẳng hạn như mô hình Người dùng. Nó chứa thông tin về
     * người nhận, chẳng hạn như địa chỉ email hoặc số điện thoại của họ.
     *
     * @return Hàm `toMail` đang trả về một đối tượng `MailMessage` với lời chào, chủ đề,
     * nội dung thư, nút hành động và thông báo hết hạn cho email đặt lại mật khẩu. Nếu một
     * Hàm `toMailCallback` đã được định nghĩa, nó sẽ được gọi với dấu `` và
     * Tham số `->token` để tạo thông báo email tùy chỉnh.
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->greeting('Dear '.$notifiable->name.'.')
            ->subject('Reset Password Notification')
            ->line('Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Vui lòng click vào nút bên dưới để thay đổi mật khẩu.')
            ->action('Reset Password', route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]))
            ->line('Liên kết đặt lại mật khẩu này sẽ hết hạn sau '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' phút.')
            ->line('Nếu bạn không yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.');
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
