@extends('personal.basic')

@section('editSection')

<div class="editor">
    <h3>基本资料</h3>

    <form class="info-form edit-form" action="{{url('personal/info')}}" method="post">
        <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
        <input name="_method" value="put" type="hidden"/>

        <div>
            <label for="username">账号</label>
            <input type="text" name="username" id="username" value="{{ $username }}" disabled/>
        </div>

        <div>
            <label for="email">邮箱</label>
            <input type="text" name="email" id="email" value="{{$email}}"/>
        </div>

        <div class="sex-select">
            <label for="sex">性别</label>
            <span><label><input type="radio" name="sex" id="sex" value="男" @if( $sex == '男' ) checked @endif/>男</label></span>
            <span><label><input type="radio" name="sex" value="女" @if( $sex == '女' ) checked @endif/>女</label></span>
        </div>

        <div>
            <label for="location">所在地</label>

            <select name="country" id="">
                <option value="">中国</option>
            </select>

            <select name="province" id="">
                <option value="">广东省</option>
            </select>

            <select name="city" id="">
                <option value="">广州市</option>
            </select>
        </div>

        <div class="description-wrapper">
            <label for="description">个人签名</label>
            <textarea name="description" id="description">{{$description}}</textarea>
        </div>

        <div class="submit-wrapper clearfix">
            <button type="submit">保存</button>
        </div>
    </form>
</div>

@endsection