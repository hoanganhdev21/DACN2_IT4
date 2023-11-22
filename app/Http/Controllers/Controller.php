<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/* Đây là một lớp PHP mở rộng bộ điều khiển cơ sở và sử dụng các đặc điểm để ủy quyền cho các yêu cầu,
gửi công việc, và xác nhận yêu cầu. */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
