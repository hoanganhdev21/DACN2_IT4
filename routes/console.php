<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

/* Đoạn mã này định nghĩa một lệnh console có tên "inspire" bằng Artisan, lệnh này khi được thực thi sẽ hiển thị
một trích dẫn đầy cảm hứng bằng cách sử dụng lớp `Inspiring`. Phương thức `->mục đích()` thiết lập mô tả của
lệnh sẽ được hiển thị khi chạy lệnh `php artisan list`.*/
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
