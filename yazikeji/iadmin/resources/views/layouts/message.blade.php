@if (count($errors) > 0)
    <script>
        var errors = '';
        @foreach ($errors->all() as $error)
                errors += errors ? '<br >{{ $error }}' : "{{ $error }}"
        @endforeach

        layui.use('layer', function(){
            var layer = layui.layer
            var message = errors
            layer.open({
                id: 'flashMessage',
                title: '错误提示',
                icon:2,
                content: message
            });
        });
    </script>
@endif


@if(\Illuminate\Support\Facades\Session::has('flashMessage'))
    <input type="hidden" id="flash_message" value="{{ session('flashMessage') }}">
    <input type="hidden" id="flash_redirectTo" value="{{ session('redirectTo') }}">
    <input type="hidden" id="flash_timeTo" value="{{ session('timeTo') }}">

    <script>
        layui.use(['layer', 'jquery'], function(){
            var layer = layui.layer
            var $ = layui.jquery
            var message = $('#flash_message').val()
            var redirectTo = $('#flash_redirectTo').val()
            var time = $('#flash_timeTo').val()

            layer.open({
                id: 'flashMessage',
                title: '信息提示',
                content: message,
                time:time,
                end: function () {
                    if (redirectTo) {
                        location.href = redirectTo
                    }
                }
            });
        });
    </script>
@endif