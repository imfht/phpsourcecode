<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
    @section("title")
    test title
    @show
    </title>


    {{-- default styles --}}
    {{HTML::style('packages/bower/bootstrap/dist/css/bootstrap.min.css')}}

    {{HTML::style('packages/bower/font-awesome/css/font-awesome.css')}}
    {{HTML::style('css/nav-style.css')}}

    @yield("styles")
</head>
<body>






@section('mainTopNav')
    @include('template.mainTopNav')
@show

@yield('content')

{{--default scripts--}}
{{--
{{HTML::script("packages/bower/jquery/dist/jquery.js")}}
{{HTML::script("packages/bower/bootstrap/dist/js/bootstrap.min.js")}}
--}}


@yield("scripts")
</body>
</html>
