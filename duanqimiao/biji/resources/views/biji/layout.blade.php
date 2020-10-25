
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        {{--移动或响应式web页面缩放设置--}}
        <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum=1.0,user-scalable=no">

        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
        <link href="{{ asset('/css/animate.css') }}" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="{{ URL::asset('/') }}js/loading.js"></script>

        <title>笔友 | Be yourself</title>
        @yield('script')
        {{--媒体查询--}}
        <style type="text/css">
            /*html 的默认 font-size=10px*/
            html{
                font-size:62.5%;
                overflow: hidden;
            }

            /* 超小屏幕（手机，小于 768px） */
            @media (max-width: 768px) {
                .col-md-1,.col-md-8{
                    display: none;
                }
            }

            /* 小屏幕（平板，大于等于 768px） */
            @media (min-width: 768px) and (max-width: 1200px) {
                .mobile_nav{
                    display: none;
                }
            }

            /* 大屏幕（大桌面显示器，大于等于 1200px） */
            @media (min-width: 1200px) {
                .mobile_nav{
                    display: none;
                }
            }
        </style>
    </head>
    <body>

    <div class="row">
        <div class="col-md-1">
                <div style="margin:3em 0 0 0.2em">
                    @yield('nav')
                </div>
        </div>
        <div class="col-md-3">
            @yield('list')
        </div>
        <div class="col-md-8">
            @yield('header')
        </div>
        <div class="col-md-8">
            @yield('content')
        </div>
    </div>
    </div>
    </body>
    </html>



