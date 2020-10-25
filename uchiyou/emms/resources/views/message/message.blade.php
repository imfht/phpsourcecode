@extends('layouts.adminFrame') 
@section('importCss') 
<link href="{{asset('css/styles/userModify.css')}}" rel="stylesheet">
@endsection


@section('content')
<div class="container">
<h3><strong>未读消息</strong>
<a class="btn btn-sucess" role="button" href="showAll">所有消息</a>
</h3>
<div class="table-responsive">
<table class="table table-hover table-bordered">
{{ $messages->links() }}
<thead>
<tr>
<th>记录</th>
<th>内容</th>
<th>操作</th>
</tr>
</thead>
<tbody>
@foreach ($messages as $message)
   	<tr>
		<td class="active info">{{$loop->index}}</td>
      	<td class="active success">  {!! $message->content !!}</td>
      	<td class="active warning"><a href="/admin/messages/{{$message->id}}/delete" class="myrequest">删除</a></td>
     </tr>
    @endforeach
</tbody>
</table>
</div>
</div>
@endsection