<!doctype html>
<html lang="zh-cmn-Hans">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (isset($siteKeywords))
    <meta name="keywords" content="{{ $siteKeywords }}">
    @else
    <meta name="keywords" content="{{ isset($CONFIG->SITE_KEYWORDS) ? $CONFIG->SITE_KEYWORDS : env('APP_NAME') }}">
    @endif
    @if (isset($siteDescription))
    <meta name="description" content="{{ $siteDescription }}">
    @else
    <meta name="description" content="{{ isset($CONFIG->SITE_DESCRIPTION) ? $CONFIG->SITE_DESCRIPTION : env('APP_NAME')
     }}">
    @endif
    @if (isset($module) && $module === 'article-show')
    <meta property="og:type" content="article">
    <meta property="og:image" content="{{ $article->cover }}">
    <meta property="og:release_date" content="{{ $article->updated_at }}">
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $siteDescription }}">
    <meta property="og:url" content="{{ action('ArticlesController@show', ['id' => $article->id]) }}">
    @endif
    <title>@section('title')@if(isset($siteTitle) && !empty($siteTitle)){{ $siteTitle }}
        - @endif{{ isset($CONFIG->SITE_TITLE) ? $CONFIG->SITE_TITLE : env('APP_NAME') }}{{ isset($CONFIG->SITE_SUB_TITLE) ? ' - ' . $CONFIG->SITE_SUB_TITLE : '' }}@show</title>
    <link rel="alternate" href="https://www.gryen.com/" hreflang="zh-Hant" />
    <link rel="stylesheet prefetch" media="screen" charset="utf-8" href={{env('STATIC_URL') . '/dist/' . config('app.version') . '/css/lib.css'}} />
    <link rel="stylesheet prefetch" media="screen" charset="utf-8" href={{env('STATIC_URL') . '/dist/' . config('app.version') . '/css/app.css'}} />
    <link rel="preload" href="{{ env('SITE_DEFAULT_IMAGE') }}" as="image">
    @include('feed::links')
    <script>
        window.Laravel = <?php echo json_encode([
                                'csrfToken' => csrf_token(),
                            ]); ?>
    </script>
    @if(env('APP_ENV') === 'production' && !Auth::check())
    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-WW9GPF5');
    </script>
    <!-- End Google Tag Manager -->
    @endif
</head>

<body @if(isset($bodyClassString))class="{{ $bodyClassString }}" @endif>

    @if(env('APP_ENV') === 'production' && !Auth::check())
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WW9GPF5" height="0" width="0" style="display:none;visibility:hidden">
        </iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    @section('base_content')
    @show
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/manifest.js'}}"></script>
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/jquery.bundle.js'}}"></script>
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/axios.bundle.js'}}"></script>
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/common.bundle.js'}}"></script>
    @if (isset($vue) && $vue)
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/vue.bundle.js'}}"></script>
    @endif
    @if (isset($module) && !isset($noJsLoad))
    <script type="text/javascript" src="{{env('STATIC_URL') . '/dist/'. config('app.version') . '/js/' . $module . '.bundle.js'}}"></script>
    @endif
</body>

</html>
