<!DOCTYPE html>
<html>
<head>
    <meta name="baidu-site-verification" content="SEGRBySjTy"/>
    <meta charset="utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="site" content=""/>
    <meta name="author" content="Ucer"/>
    <title>@yield('title'){{ config('app.name') }} </title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body class="pushable">
<div id="app2">
    @include('layouts.common.navbar')
</div>
<div class="main container pusher" id="app">
    @yield('content')
    <messages message-session="{{ session()->get('toastrMsg.message') }}"
              message-type="{{ session()->get('toastrMsg.status') }}"></messages>
</div>
@include('layouts.common.footer')

<script src="{{ mix('js/app.js') }}"></script>
<script src="{{ mix('js/jquery.js') }}"></script>

@yield('script')

</body>

</html>
