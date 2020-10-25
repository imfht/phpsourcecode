@extends('layouts.manageFrame')

@section('importCss')
<link href="{{ asset('css/plugins/jsTree/themes/default/style.css') }}" rel="stylesheet">
@endsection


@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    You are logged in!
                    <br>
                    <a href="/aa/admin/treeTrunk/create">增加物资种类信息</a><br>
                    <a href="/aa/admin/material">物资信息</a>
                    
<hr>               
<table>
@foreach ($trunks as $trunk)
	<tr>
    <td>{{ $trunk->name }}</td>
    <td>{{ $trunk->type }}</td>
    <td>{{ $trunk->number }}</td>
    <td>{{ $trunk->description }}</td>
  </tr>
		@endforeach
</table>

                </div>
            </div>
        </div>
    </div>
</div>



  <div class="nav-item-content" id="treesidebar" style="height:100%;overflow: auto">
        </div>
@endsection
@section('importJs')
<script type="text/javascript" src="{{asset('js/plugins/jsTree/jstree.js')}}"></script>
<script src="{{ asset('js/plugins/jsTree/mytreeOperate.js') }}"></script>
@endsection