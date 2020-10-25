@extends('layouts.base')

@section('content')
    <div class="get">
        <div class="am-g">
            <div class="am-u-lg-12">
                <h1 class="get-title">Lucms 一套完整的前后端开发框架</h1>

                <p>
                    欢迎 start.
                </p>

                <p>
                    <a href="http://lucms.codehaoshi.com/dashboard" class="am-btn am-btn-sm get-btn">线上 demo</a>
                </p>
            </div>
        </div>
    </div>

    <div class="detail">
        <div class="am-g am-container">
            <div class="am-u-lg-12">
                <h2 class="detail-h2">系统简介</h2>

                <div class="am-g">
                    <div class="am-u-lg-4 am-u-md-6 am-u-sm-12 detail-mb">

                        <h3 class="detail-h3">
                            <i class="am-icon-chrome am-icon-sm"></i>
                            代码优雅
                        </h3>

                        <p class="detail-p">
                            Lucms 后端基于 laravel 框架开发，号称是 php 最简洁优雅的代码,代码简洁规范。
                        </p>
                    </div>
                    <div class="am-u-lg-4 am-u-md-6 am-u-sm-12 detail-mb">
                        <h3 class="detail-h3">
                            <i class="am-icon-cloud am-icon-sm"></i>
                            体验好
                        </h3>

                        <p class="detail-p">
                            Lucms 采用了 iviewjs 开作为前端框架，实现了前后端的完全分离。
                        </p>
                    </div>
                    <div class="am-u-lg-4 am-u-md-6 am-u-sm-12 detail-mb">
                        <h3 class="detail-h3">
                            <i class="am-icon-gg am-icon-sm"></i>
                            新的技术
                        </h3>

                        <p class="detail-p">
                            Lucms 抛开了老旧的开发模式，引入了 npm、composer 等好用并利于管理项目的技术。
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="am-g am-container">
            <div class="am-u-lg-18">
                <ul data-am-widget="gallery" class="am-gallery am-avg-sm-2
  am-avg-md-3 am-avg-lg-4 am-gallery-default" data-am-gallery="{ pureview: true }">
                    <li>
                        <div class="am-gallery-item">
                            <a href="https://images.gitee.com/uploads/images/2018/1020/105231_0eaf6774_923445.png"
                               class="">
                                <img src="https://images.gitee.com/uploads/images/2018/1020/105231_0eaf6774_923445.png"
                                     alt="用户列表"/>
                                <h3 class="am-gallery-title">用户列表</h3>
                                <div class="am-gallery-desc">{{ date('Y-m-d') }}</div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="am-gallery-item">
                            <a href="https://images.gitee.com/uploads/images/2018/1020/105351_4431299a_923445.png"
                               class="">
                                <img src="https://images.gitee.com/uploads/images/2018/1020/105351_4431299a_923445.png"
                                     alt="添加用户"/>
                                <h3 class="am-gallery-title">添加用户</h3>
                                <div class="am-gallery-desc">{{ date('Y-m-d') }}</div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="am-gallery-item">
                            <a href="https://images.gitee.com/uploads/images/2018/1020/105416_418a8c8e_923445.png"
                               class="">
                                <img src="https://images.gitee.com/uploads/images/2018/1020/105416_418a8c8e_923445.png"
                                     alt="Wang"/>
                                <h3 class="am-gallery-title">Wang</h3>
                                <div class="am-gallery-desc">{{ date('Y-m-d') }}</div>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="am-gallery-item">
                            <a href="https://images.gitee.com/uploads/images/2018/1020/105432_2c3de407_923445.png"
                               class="">
                                <img src="https://images.gitee.com/uploads/images/2018/1020/105432_2c3de407_923445.png"
                                     alt="Markdown"/>
                                <h3 class="am-gallery-title">Markdown</h3>
                                <div class="am-gallery-desc">{{ date('Y-m-d') }}</div>
                            </a>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
    </div>


@endsection

@section('script')
    <script>
    </script>
@endsection
