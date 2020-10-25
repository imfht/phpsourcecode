@extends('BackTheme::layout.master')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">日志报告</h3>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="/admin/report/logs">用户操作日志</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


@stop

