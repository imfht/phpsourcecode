@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='/css/about.css' />
<link rel='stylesheet' href='/css/effects.css' />
@endsection

@section('content')
<div class='well logo-well'>
    <img class='logo-img' src='/img/logo.png' />
</div>

<div class='about-contents'>
    @if ($role == "admin")
    <dl>
        <p>网站信息</p>
        <dt>版本： {{env('POLR_VERSION')}}</dt>
        <dt>创建时间： {{env('POLR_RELDATE')}}</dt>
        <dt>安装时间： {{env('APP_NAME')}} on {{env('APP_ADDRESS')}} on {{env('POLR_GENERATED_AT')}}<dt>
    </dl>
    @endif

    <p>{{env('APP_NAME')}} 由 Polr 2 驱动， Polr 2 是一个极简的短地址压缩开源程序。</p>
    <p>更多信息请点击项目主页：<a href='https://github.com/Cydrobolt/polr' target="_blank">Github page</a> 或者： <a href="//project.polr.me" target="_blank">作者主页</a>.
        <br />Polr 遵循 GNU GPL License 协议。
    </p>
</div>
<a href='#' class='btn btn-success license-btn'>更多信息</a>
<pre class="license" id="gpl-license">
Copyright (C) 2013-2017 Chaoyi Zha

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
</pre>

@endsection

@section('js')
<script src='/js/about.js'></script>
@endsection
