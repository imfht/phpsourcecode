<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h3>密码重置 --{{$siteName}}</h3>

        <div>
            请通过点击 <a href="{{ URL::to('password/reset', array($token)) }}"> 密码重置</a> 链接进行密码重置。<br/>
            <br/>
            如果该链接无法点击，请复制以下URL链接到浏览器的地址栏进行相应操作。<br/>
            {{ URL::to('password/reset', array($token)) }}<br/>
            <br/>
            该链接将在 {{ Config::get('auth.reminder.expire', 60) }} 分钟后失效。<br/>
            <br/>
            <br/>
            请不要回复该邮件！<br/>
            --{{$siteName}}
        </div>
    </body>
</html>
