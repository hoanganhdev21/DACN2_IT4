<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

/* Lớp TrimStrings là một phần mềm trung gian trong PHP cắt tất cả đầu vào chuỗi ngoại trừ đầu vào cụ thể
thuộc tính. */
class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array<int, string>
     */
    /* Thuộc tính `protected ` là một mảng xác định tên của các thuộc tính mà
    không nên bị cắt bớt bởi phần mềm trung gian `TrimStrings`. Trong trường hợp này, `current_password`,
    Các thuộc tính `password` và `password_confirmation` không bị cắt bớt. Điều này có nghĩa là
    rằng mọi khoảng trắng ở đầu hoặc cuối trong các thuộc tính này sẽ không bị xóa. */
    protected $except = [
        'current_password',
        'password',
        'password_confirmation',
    ];
}
