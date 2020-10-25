<!doctype html>

<html lang="zh-cmn-hans" id="TeamMindmap">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf_token" content="{{ csrf_token() }}" />
    <title ng-bind="title" ></title>
    <style>
        /*-------------for ngApp initial loading animation----------------*/

        .ngApp-init-loading .loading-info{
            color: #86ccad;
            width: 12em;
            font-size: 1.3em;
            text-indent: 3em;
            margin: auto;
        }

        .spinner {
            margin: auto;
            margin-top: 150px;
            margin-bottom: 50px;
            width: 190px;
            height: 190px;
            position: relative;
            text-align: center;

            -webkit-animation: rotate 2.0s infinite linear;
            animation: rotate 2.0s infinite linear;
        }

        .dot1, .dot2 {
            width: 60%;
            height: 60%;
            display: inline-block;
            position: absolute;
            top: 0;
            background-color: #99CCCC;
            border-radius: 100%;

            -webkit-animation: bounce 2.0s infinite ease-in-out;
            animation: bounce 2.0s infinite ease-in-out;
        }

        .dot2 {
            top: auto;
            bottom: 0px;
            -webkit-animation-delay: -1.0s;
            animation-delay: -1.0s;
        }

        @-webkit-keyframes rotate { 100% { -webkit-transform: rotate(360deg) }}
        @keyframes rotate { 100% { transform: rotate(360deg); -webkit-transform: rotate(360deg) }}

        @-webkit-keyframes bounce {
            0%, 100% { -webkit-transform: scale(0.0) }
            50% { -webkit-transform: scale(1.0) }
        }

        @keyframes bounce {
            0%, 100% {
                transform: scale(0.0);
                -webkit-transform: scale(0.0);
            } 50% {
                  transform: scale(1.0);
                  -webkit-transform: scale(1.0);
              }
        }
    </style>

@if( Config::get('app.debug') )
{{-- default styles --}}
{{HTML::style('packages/bower/bootstrap/dist/css/bootstrap.min.css')}}
{{HTML::style('css/nav-style.css')}}

{{--插件样式--}}
{{HTML::style('packages/bower/bxslider-4/jquery.bxslider.css')}}
{{HTML::style('ngApp/common/localResizeIMG-2/build/localResize.css')}}
{{HTML::style('packages/bower/angular-toasty/css/ng-toasty.css')}}


{{--通用样式--}}
{{HTML::style('css/app-common.css')}}
{{HTML::style('css/ngCommon/third-nav-style.css')}}
{{HTML::style('css/ngCommon/label-list-box.css')}}

{{--片段样式--}}

{{HTML::style('ngApp/project/css/project-selection-common.css')}}
{{HTML::style('ngApp/project/css/create-task-style.css')}}
{{HTML::style('ngApp/project/css/member-selection-style.css')}}


{{--具体文件对应样式--}}
{{--project样式--}}
{{HTML::style('ngApp/project/css/project-list-style.css')}}
{{HTML::style('ngApp/project/css/project-member-style.css')}}
{{HTML::style('ngApp/project/css/project-creating-style.css')}}
{{HTML::style('ngApp/project/css/project-desktop.css')}}
{{HTML::style('ngApp/project/css/task-style.css')}}
{{HTML::style('ngApp/project/css/task-info-style.css')}}
{{HTML::style('ngApp/project/css/sidebar-style.css')}}
{{HTML::style('ngApp/project/css/project-discussion.css')}}


{{--personal样式--}}
{{HTML::style('ngApp/personal/css/information-common.css')}}
{{HTML::style('ngApp/personal/css/personal-template-style.css')}}
{{HTML::style('ngApp/personal/css/second-nav-style.css')}}
{{--HTML::style('ngApp/personal/css/third-nav-style.css')--}}

{{--personal.notification样式--}}
{{HTML::style('ngApp/personalNotification/css/notification-index-style.css')}}

{{-- personal.message --}}
{{HTML::style('ngApp/personalMessage/css/message-common.css')}}
{{HTML::style('ngApp/personalMessage/css/message-list.css') }}
{{HTML::style('ngApp/personalMessage/css/message-creating-style.css')}}
{{HTML::style('ngApp/personalMessage/css/message-show-style.css')}}


{{-- project.sharing样式 --}}
{{HTML::style('ngApp/project/css/sharing-creating.css')}}
{{HTML::style('ngApp/project/css/sharing-info.css')}}
{{HTML::style('ngApp/project/css/sharing-list.css')}}
{{HTML::style('ngApp/project/css/sharing-deckgrid.css')}}
{{HTML::style('ngApp/project/css/project-switch-loading.css')}}
{{HTML::style('ngApp/project/css/project-setting.css')}}

{{-- mindmap样式 --}}
{{HTML::style('ngApp/mindmap/css/mindmap.css')}}

{{--font-awesome字符库可能会与其他样式库冲突,所以放在最后--}}
{{HTML::style('packages/bower/font-awesome/css/font-awesome.css')}}

{{--Bootstrap Markdown的样式文件 --}}
{{HTML::style('packages/bower/bootstrap-markdown/css/bootstrap-markdown.min.css') }}

@else
    {{HTML::style('css/min.css')}}
@endif

<body>

<div class="ngApp-init-loading">

    <div class="spinner">
        <div class="dot1"></div>
        <div class="dot2"></div>
    </div>

    <p class="loading-info">资源加载中...</p>
</div>



<main-top-nav function-nav-items="functionNavItems"></main-top-nav>

<ui-view></ui-view>

@section('script')
    @if( Config::get('app.debug') )
        {{HTML::script('/packages/bower/requirejs/require.js',array('data-main'=>'../ngApp/ng-require-mainApp.js'))}}
    @else
        {{HTML::script('packages/bower/requirejs/require.js', array('data-main'=>'/ngApp/ng-main.min.js'))}}
    @endif
@show
</body>
</html>