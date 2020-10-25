@extends('emails.layout')

@section('title')找回用户密码@endsection

@section('content')
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="content-block">
                如果您在{{ Setting()->get('website_name') }}的密码丢失，请点击下方链接找回:
            </td>
        </tr>
        <tr>
            <td class="content-block">
                {{ route('auth.user.findPassword',['token'=>$token]) }}
            </td>
        </tr>
        <tr>
            <td class="content-block">
                &mdash; {{ Setting()->get('website_name') }}
            </td>
        </tr>
    </table>
@endsection
