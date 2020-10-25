@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/reset_password.css' />
@endsection

@section('content')
<h1 class='header'>重置密码</h1>

<div class='col-md-6 col-md-offset-3'>
    <form action="#" method='POST'>
        <input type='password' id='passwordFirst' placeholder='新密码' class='form-control password-input-pd'>
        <input type='password' id='passwordConfirm' placeholder='再次输入新密码' class='form-control password-input-pd' name='new_password'>

        <input type="hidden" name='_token' value='{{csrf_token()}}' />
        <input type='submit' id='submitForm' value='Reset Password' class='form-control'>
    </form>
</div>
@endsection

@section('js')
<script src='/js/reset_password.js'></script>
@endsection
