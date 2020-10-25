@extends('layouts.admin_template')

@section('content')
    <div class='row'>
        <div class='col-md-8'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@if($function=='create')新建@else更新@endif角色</h3>
                </div>
                @if($function=='create')
                    {!! Form::open(['route' => 'role.store']) !!}
                @else
                    {!! Form::model($role, ['route' => ['role.update', $role->id],'method' => 'put']) !!}
                @endif
                <div class="box-body">
                    @include('errors.submitError')
                    {{ Form::bsText('name','角色名称') }}
                    {{ Form::bsText('display_name','显示名称') }}
                    {{ Form::bsTextArea('description','描述',null,['style'=>'height:4em']) }}

                </div><!-- /.box-body -->
                <div class="box-footer">
                    {{ Form::bsButton('cancel','btn-default','取消',route('role.index')) }}
                    {{ Form::bsButton('submit','btn-info pull-right','保存') }}
                </div><!-- /.box-footer-->
                {!! Form::close() !!}
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection