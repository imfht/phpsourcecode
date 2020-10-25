<h2>中文</h2>
<h3>{{$username}} 恭喜您！</h3>

<p>感谢您在 {{env('APP_NAME')}} 注册账号。 请点击下面的链接进行激活：</p>

<br />

<a href='{{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}/activate/{{$username}}/{{$recovery_key}}'>
    {{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}/activate/{{$username}}/{{$recovery_key}}
</a>

<br />

<p>谢谢！</p>
<p>{{env('APP_NAME')}} 团队。</p>

--
<br />
You received this email because someone with the IP {{$ip}} signed up
for an account at {{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}. If this was not you,
you may ignore this email.

<h2>English</h2>
<h3>Hello {{$username}}!</h3>

<p>Thanks for registering at {{env('APP_NAME')}}. To use your account,
you will need to activate it by clicking the following link:</p>

<br />

<a href='{{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}/activate/{{$username}}/{{$recovery_key}}'>
    {{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}/activate/{{$username}}/{{$recovery_key}}
</a>

<br />

<p>Thanks,</p>
<p>The {{env('APP_NAME')}} team.</p>

--
<br />
You received this email because someone with the IP {{$ip}} signed up
for an account at {{env('APP_PROTOCOL')}}{{env('APP_ADDRESS')}}. If this was not you,
you may ignore this email.
