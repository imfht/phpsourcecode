@extends('log-viewer::layout')

@section('log-viewer::title', 'Laravel Log Viewer')

@section('log-viewer::content')
    <div class="row">
        <div class="col-lg-12">
            <div class="page-header">
                <h1>{{ trans('log-viewer::log-viewer.info.info_title') }}</h1>
                <div class="header-btns">
                    <a href="{{ route('log-viewer-download')}}?file={{ $service->getLogName() }}" class="btn hidden-xs btn-sm btn-success" style="margin-right: 3px;"><i class="fa fa-fw fa-download"></i> {{ trans('log-viewer::log-viewer.info.download_label') }}</a>
                    <a href="javascript:;" data-url="{{route('log-viewer-delete')}}?file={{ $service->getLogName() }}" class="btn hidden-xs btn-sm btn-danger delete-btn"><i class="fa fa-fw fa-trash-o"></i> {{ trans('log-viewer::log-viewer.info.delete_label') }}</a>
                </div>
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <div class="row">

        <div class="col-lg-12">

            <!-- /.panel -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <ul class="list-inline">
                        <li><label>{{ trans('log-viewer::log-viewer.info.log_path') }}：</label>{{ $service->getLogName() }}</li>
                    </ul>
                    <ul class="list-inline">
                        <li><label>{{ trans('log-viewer::log-viewer.info.log_entries') }}：</label>{{ count($service->getLogContents()) }}</li>
                        <li><label>{{ trans('log-viewer::log-viewer.info.log_size') }}：</label>{{ $service->getLogSize() }}</li>
                        <li><label>{{ trans('log-viewer::log-viewer.info.modified_at') }}：</label>{{ $service->getLogModified() }}</li>
                    </ul>

                </div>
            </div>
            <!-- /.panel -->

        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <!-- /.panel-heading -->
                <div class="panel-body">

                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-log-viewer">
                        <thead>
                        <tr>
                            <th>{{ trans('log-viewer::log-viewer.info.log_level') }}</th>
                            <th>{{ trans('log-viewer::log-viewer.info.log_env') }}</th>
                            <th>{{ trans('log-viewer::log-viewer.info.log_datetime') }}</th>
                            <th>{{ trans('log-viewer::log-viewer.info.log_content') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($service->getLogContents() as $content)
                            <tr class="odd gradeX">
                                <td class="{{ $service->getLevelColor($content['level']) }}"><i class="fa fa-fw {{ $service->getLevelIcon($content['level']) }}"></i> {{ $content['level'] }}</td>
                                <td>{{ $content['env'] }}</td>
                                <td>{{ $content['datetime'] }}</td>
                                <td class="center">{{ $content['message'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!-- /.table-responsive -->

                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
@endsection

@section("log-viewer::script")
    @parent
    <script>
        $(document).ready(function() {
            $('#side-menu').metisMenu();

            $('#dataTables-log-viewer').DataTable({
                "responsive": true,
                "processing": true,
                "ordering": true,
                "order": [],
                "fixedHeader": "{{ config('log-viewer.fix_header') }}",
                "lengthChange": true,
                "info":true,
                "columnDefs": [
                    { "width": "10%", "targets": 0 },
                    { "width": "10%", "targets": 1 },
                    { "width": "20%", "targets": 2 }
                ],
                "pagingType": "full_numbers",
                "lengthMenu": [{{ config('log-viewer.page_size_menu') }}],
                "pageLength": {{ config('log-viewer.default_page_size') }},
                "language": @json(trans('log-viewer::log-viewer.table'))
            });

            $(".delete-btn").click(function () {
                var thisObj = $(this);
                confirm("{{ trans('log-viewer::log-viewer.confirm.confirm_content') }}", function () {
                    $.get(thisObj.data('url'), function (result) {
                        if(result.status == 'success'){
                            window.location.href = result.redirect;
                        }else{
                            alert(result.message, 'error', function () {
                                window.location.reload();
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
