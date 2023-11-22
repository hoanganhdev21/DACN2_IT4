<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/* Lớp AuthServiceProvider chịu trách nhiệm đăng ký xác thực và ủy quyền
dịch vụ trong một ứng dụng PHP. */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * Mô hình để ánh xạ chính sách cho ứng dụng.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     * Đăng ký bất kỳ dịch vụ xác thực / ủy quyền nào.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
