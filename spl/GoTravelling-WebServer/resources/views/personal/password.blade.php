@extends('personal.basic')

@section('editSection')
<div class="editor">
    <h3>密码设置</h3>

    <form action="{{url('personal/password')}}" class="edit-form password-form" method="post">
        <input name="_method" value="put" type="hidden"/>
        <input name="_token" type="hidden" value="{{ csrf_token() }}"/>

        <div>
            <label for="password">当前密码</label>
            <input type="password" name="password" id="password" required/>
            <span class="error-input">{{$errors->first('password')}}</span>
        </div>

        <div>
            <label for="newPassword">新密码</label>
            <input type="password" name="newPassword" id="newPassword" required/>
            <span class="error-input">{{$errors->first('newPassword')}}</span>
        </div>
        
        <div>
            <label for="newPassword_confirmation">确认密码</label>
            <input type="password" name="newPassword_confirmation" id="newPassword_confirmation" required/>
            <span class="error-input">{{$errors->first('newPassword_confirmation')}}</span>
        </div>

        <div class="submit-wrapper">
            <button type="submit">提交</button>
        </div>
    </form>
</div>
@endsection