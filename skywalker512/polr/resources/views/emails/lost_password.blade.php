<h2>中文</h2>
<h3>亲爱的 {{$username}} ！</h3>

<p>
    请点击下面的链接来重置您的密码！ {{env('APP_NAME')}}.
</p>

<a href='{{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}/reset_password/{{$username}}/{{$recovery_key}}'>
    {{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}/reset_password/{{$username}}/{{$recovery_key}}
</a>

<br />

<p>感谢您的使用！</p>
<p>{{env('APP_NAME')}} 团队</p>

--
<br />
因为有人在{{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}请求重置密码，所以您才会收到这封邮件。
请求来自：IP {{$ip}}
如果不是您本人操作，请忽视本邮件，并提醒您修改您的密码。

<h2>English</h2>
<h3>Hello {{$username}}!</h3>

<p>
    You may use the link located in this email to reset your password for your
    account at {{env('APP_NAME')}}.
</p>

<a href='{{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}/reset_password/{{$username}}/{{$recovery_key}}'>
    {{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}/reset_password/{{$username}}/{{$recovery_key}}
</a>

<br />

<p>Thanks,</p>
<p>The {{env('APP_NAME')}} team.</p>

--
<br />
You received this email because someone with the IP {{$ip}} requested a password reset
for an account at {{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}. If this was not you,
you may ignore this email.
