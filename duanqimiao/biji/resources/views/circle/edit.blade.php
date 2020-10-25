@extends('circle.layout')
@section('script')

    <script language="JavaScript" src="{{ URL::asset('/') }}js/circle.js" xmlns="http://www.w3.org/1999/html"></script>


    <link type="text/css" href="{{ asset('/css/circle.css') }}" rel="stylesheet"/>

@endsection

@section('content')
    <h3><a style=";color: #666666" href="/circle/">笔友圈</a> <small>» 查看笔记 </small></h3>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">{{ $biji -> title }}</h3>
        </div>
        <div class="panel-body">
            {!! $biji -> content !!}
            <h5>
                发布于: {{ $biji -> published_at }}

                作者: {{ $user->name }}

                <input type="hidden" name="user_id" value="{{ Auth::id() }}"/>

                <i class="icon collect-img"></i><a class="collect">收藏</a>

                <i class="icon good-img"></i><a class="good">点赞</a>
            </h5>
            <div>
                <h6>
                    <input type="hidden" name="biji_id" value="{{ $biji->id }}">
                    <input type="hidden" name="biji_title" value="{{ $biji->title }}">
                    <input type="hidden" name="reporter_name" value="{{ $currentUser->name }}">
                    <input type="hidden" name="reported_id" value="{{ $user->id }}">
                    <input type="hidden" name="reported_name" value="{{ $user->name }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a class="report"><span class="glyphicon glyphicon-flag"></span> 涉及不合法内容？举报该用户</a>
                </h6>
            </div>
            <!-- JiaThis Button BEGIN -->
            <div class="jiathis_style" style="float: right">
                <span class="jiathis_txt">分享到：</span>
                <a class="jiathis_button_tools_1"></a>
                <a class="jiathis_button_tools_2"></a>
                <a class="jiathis_button_tools_3"></a>
                <a class="jiathis_button_tools_4"></a>
                <a href="http://www.jiathis.com/share" class="jiathis jiathis_txt jiathis_separator jtico jtico_jiathis" target="_blank">更多</a>
                <a class="jiathis_counter_style"></a>
            </div>
            <script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>
            <!-- JiaThis Button END -->
        </div>

    </div>
    {{--评论区--}}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">评论</h3>
        </div>

        <div class="panel-body">

            <div class="comment-filed">
                <!--发表评论区begin-->
                <div>

                    <div>
                        <div class="form-group">
                            <textarea class="form-control txt-commit" replyid="0"></textarea>
                            <input type="hidden" name="biji_id" value="{{ $biji->id }}">
                        </div>
                        <div class="div-txt-submit">
                            <a class="comment-submit" parent_id="0" style="" href="javascript:void(0);"><button class="btn btn-primary">发表评论</button></a>
                        </div>
                    </div>
                </div>
                <!--发表评论区end-->

                <!--评论列表显示区begin-->
                <div><span>全部评论</span><hr/></div> <br/><br/>
                <div class="comment-filed-list" >
                    <div class="comment-list" >
                        <!--一级评论列表begin-->
                        @foreach($parent_comments as $comment)
                            <ul class="comment-ul">
                                @if($parent_comments->isEmpty())
                                @else
                                    <li comment_id="{{ $comment->id }}">
                                        <div>
                                            <div class="cm">
                                                <div class="cm-header">
                                                    <span>{{ $comment->user_name }}</span>
                                                    <span>{{ $comment->created_at }}</span>
                                                </div>
                                                <div class="cm-content">
                                                    <p>
                                                        {{ $comment->comments }}
                                                    </p>
                                                </div>
                                                <div class="cm-footer">
                                                    <a class="comment-reply" comment_id="{{ $comment->id }}" href="javascript:void(0);">回复</a>
                                                </div>
                                            </div>
                                        </div>

                                        <!--二级评论begin-->
                                        <div style="display: none">{{ $parent_id[] = $comment->id }}</div>
                                        @for($i = 0;$i<count($parent_id);$i++)
                                            {{--获得子评论的资源句柄--}}
                                            <div style="display: none">{{ $children = \App\Comment::where('biji_id',$biji->id)->where('parent_id',$parent_id[$i])->get() }}</div>
                                        @endfor
                                        @foreach($children as $child)
                                            <ul class="children">
                                                <li comment_id="{{ $child->id }}">
                                                    <div>
                                                        <div class="children-cm">
                                                            <div  class="cm-header">
                                                                <span>{{ $child->user_name }}</span>
                                                                <span>{{ $child->created_at }}</span>
                                                            </div>
                                                            <div class="cm-content">
                                                                <p>
                                                                    {{ $child->comments }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                            @endforeach
                                                    <!--二级评论end-->
                                    </li>
                                @endif
                            </ul>
                            @endforeach
                                    <!--一级评论列表end-->
                    </div>
                </div>
                <!--评论列表显示区end-->
            </div>
        </div>
    </div>
@endsection
