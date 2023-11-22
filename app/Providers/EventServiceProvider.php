<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

/* Lớp EventServiceProvider ánh xạ các sự kiện tới các bộ lắng nghe cho một ứng dụng PHP. */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     * Ánh xạ sự kiện tới người nghe cho ứng dụng.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     * Đăng ký bất kỳ sự kiện nào cho ứng dụng của bạn.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     * Xác định xem các sự kiện và người nghe có được phát hiện tự động hay không.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
