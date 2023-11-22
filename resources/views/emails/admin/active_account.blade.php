@component('mail::message')
# Xin Chào!
@if($data['password'])
Mật khẩu truy cập hệ thống <a href="{{ route('home_page') }}" ><b>VDO</b></a> của bạn là <b>{{ $data['password'] }}</b>.<br>Vui lòng click vào button bên dưới để kích hoạt tài khoản. Sau đó thay đổi mật khẩu và cập nhật lại thông tin cá nhân.<br>Xin cảm ơn!
@else
Đây là email kích hoạt tài khoản. Vui lòng click vào button bên dưới để kích hoạt tài khoản.<br>Xin cảm ơn!
@endif
@component('mail::button', ['url' => route('active_account', ['token' => $data['token']])])
Active Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
