@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-sm-9" id="article">
                    <div class="page-header text-center">
                        <h2>{{ $article->title }}</h2>
                        @if($article->subtitle != '')
                            <h4>{{ $article->subtitle }}</h4>
                        @endif
                        <span>作者：</span>{{ $article->author }}
                        <span>来源：</span>{{ $article->source }}
                        <span>发布时间：</span>{{ $article->maketime() }}
                        <span>浏览量：</span>{{ $article->views }}
                        @if($is_favorite)
                            <span class="glyphicon glyphicon-heart pull-right">收藏</span>
                        @else
                            <span class="glyphicon glyphicon-heart-empty pull-right">收藏</span>
                        @endif
                    </div>
                    <div class="page-body">
                        {!! $article->text !!}
                    </div>
                    <ul class="list-group comment-list">
                        <li class="list-group-item active">留言：</li>
                    @foreach ($comments as $comment)
                        <li class="list-group-item">
                            <p>{{ $comment->name }}:</p>
                            <span>{{ $comment->info }}</span>
                            <span class="pull-right">{{ $comment->maketime() }}</span>
                            <div class="clearfix"></div>
                        </li>
                    @endforeach
                    </ul>
                    <div class="jumbotron">
                        <form id="comment-up" class="comment" action="{{ url('comment') }}" method="post">
                            {!! csrf_field() !!}
                            <input type="hidden" name='article_id' value='{{ $article->id }}'>
                            <div class="form-inline">
                                <div class="form-group col-sm-6">
                                    <label for="name">姓名</label>
                                    <input type="text" class="form-control" id="name" name='name' placeholder="">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label for="phone">手机/邮箱</label>
                                    <input type="email" class="form-control" id="phone" name='phone' placeholder="">
                                </div>
                            </div>
                            <div class="form-group col-sm-12">
                                <label for="info">内容</label>
                                <div class="">
                                    <textarea class="form-control" rows="3" name='info' id='info'></textarea>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 text-right">
                                <button type="button" class="btn btn-default comment-up">提交</button>
                            </div>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                    <div class="page-footer clearfix">
                        <p>相关阅读：</p>
                        @if($articles = $type->articles->sortByDesc('id')->take(6))
                            @foreach($articles as $article)
                            <div class="col-sm-6">
                                <a href="{{ url('article',$article->id) }}">{{ $article->title }}</a>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-sm-3">
                    @include($theme.'.category.right')
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () {
            var token = $("input[name='_token']").val();
            var article_id = $("input[name='article_id']").val();
            var url = $("#comment-up").attr('action');

            //留言提交
            if( $("#comment-up").length > 0 ) {
                $(document).on( "click", ".comment-up", function() {
                    var name = $("input[name='name']").val();
                    var phone = $("input[name='phone']").val();
                    var info = $("textarea[name='info']").val();
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: { _token: token, article_id: article_id, name: name, phone: phone, info: info},
                        success: function (data) {
                            alert(data.message);
                            if(data.error==0){
                                $("input[name='name']").val('');
                                $("input[name='phone']").val('');
                                $("textarea[name='info']").val('');
                            }
                        },
                        error: function (data) {
                            alert('提交失败，请刷新页面！');
                            location.reload();
                        }
                    });
                });
            }

            //收藏
            $(document).on( "click", ".glyphicon-heart-empty", function() {
                $.ajax({
                    type: 'POST',
                    url: '/user/favorite',
                    data: { _token: token, article_id: article_id, model: 'article', type: 'add'},
                    success: function (data) {
                        alert(data.message);
                        $('.glyphicon-heart-empty').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                    },
                    error: function (data) {
                        alert('收藏失败！请刷新！');
                    }
                });
            });
            $(document).on( "click", ".glyphicon-heart", function() {
                $.ajax({
                    type: 'POST',
                    url: '/user/favorite',
                    data: { _token: token, article_id: article_id, model: 'article', type: 'del'},
                    success: function (data) {
                        alert(data.message);
                        $('.glyphicon-heart').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                    },
                    error: function (data) {
                        alert('取消失败！请刷新！');
                    }
                });
            });
        })
    </script>
@endsection