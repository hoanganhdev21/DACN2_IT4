<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

/* Đây là một lớp PHP để ngăn chặn các yêu cầu trong chế độ bảo trì, với khả năng chỉ định
ngoại lệ cho một số URI nhất định. */
class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
