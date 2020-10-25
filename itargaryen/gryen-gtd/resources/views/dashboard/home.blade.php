<!doctype html>
<html lang="zh-cmn-Hans">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>仪表盘 - {{ isset($CONFIG->SITE_TITLE) ? $CONFIG->SITE_TITLE : env('APP_NAME') }}</title>
    <link rel="stylesheet prefetch" media="screen" charset="utf-8" href={{env('STATIC_URL') . '/dist/' . config('app.version') . '/css/dashboard.css'}} />
    <script>
        window.Laravel = <?php echo json_encode([
                                'csrfToken' => csrf_token(),
                            ]); ?>
    </script>
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/manifest.js'}}" async="async"></script>
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/axios.bundle.js'}}" async="async"></script>
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/dashboard.bundle.js'}}" async="async"></script>
</head>

<body>
    <div id="dashboardApp">
        <dashboard-home></dashboard-home>
    </div>
</body>

</html>
