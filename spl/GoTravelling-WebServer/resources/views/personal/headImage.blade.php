@extends('personal.basic')

@section('editSection')
    <div class="editor">
        <h3>我的头像</h3>

        <form class="edit-form head-form" action="{{url('personal/head-image')}}" method="post" enctype="multipart/form-data">

            <div class="head-image">
                @if( Auth::user()['head_image'] )
                    <img id="head-image" src="{{asset('image/header/'. Auth::user()['head_image'])}}" alt="用户头像"/>
                @else
                    <img id="head-image" src="{{asset('image/header/default_head_image.png')}}" alt="用户头像"/>
                @endif
            </div>

            <div class="head-image-upload">
                <button class="upload-button">更改头像</button>
                <input class="upload-input" type="file" name="head_image" id="img-upload-input" />
            </div>

            <div class="submit-wrapper">
                <button type="submit">保存</button>
            </div>
        </form>
    </div>
@endsection
