<!DOCTYPE html>
<html>
<head>
    <title>{{ seo('title') }}</title>
    <meta charset=utf-8>
    <meta http-equiv=X-UA-Compatible content="IE=edge">
    <meta name="keywords" content="{{ seo('keywords') }}">
    <meta name="description" content="{{ seo('description') }}">
    <link href="{{ asset('assets/mall/foreground/css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app"></div>
<script>
    window.api = "{{ url('api') }}";
</script>
<script type="text/javascript" src="{{ asset('assets/mall/foreground/js/manifest.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/mall/foreground/js/vendor.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/mall/foreground/js/app.js') }}"></script>
</body>
</html>