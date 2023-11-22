<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

/* Lớp EncryptCookies là phần mềm trung gian cho phép mã hóa cookie, với khả năng
loại trừ một số cookie khỏi mã hóa. */
class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}
