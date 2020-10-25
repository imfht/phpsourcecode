@extends('layouts.admin_template')

@section('content')
    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@if($function=='create')新建@else更新@endif模块</h3>
                </div>
                @if($function=='create')
                    {!! Form::open(['route' => 'permission.store']) !!}
                @else
                    {!! Form::model($permission, ['route' => ['permission.update', $permission->id],'method' => 'put']) !!}
                @endif
                <div class="box-body">
                    @include('errors.submitError')
                    {{ Form::bsText('name','模块名称') }}
                    {{ Form::bsText('display_name','显示名称') }}
                    {{ Form::bsTextArea('description','描述',null,['style'=>'height:4em']) }}

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{ Form::bsButton('cancel','btn-default','取消',route('permission.index')) }}
                    {{ Form::bsButton('submit','btn-info pull-right','保存') }}
                </div><!-- /.box-footer-->
                {!! Form::close() !!}
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection