<style>
    .header{
        font-size: 1.8rem;
        color:#ABABAB;
        margin: 20px 0 ;
        border-bottom: 1px solid #ECECEC;
        padding: 10px;
    }
    .atip:hover{
        color: #337ab7;
    }
    #info_form{
        display: inline
    }
</style>

<div class="header">
    <form id="info_form" method="GET" action="{{ url('/biji/'.$list->id) }}">
        <button type="submit" id="info" class="btn btn-info btn-sm" >
            <i class="fa fa-times-circle"></i>
            笔记信息
        </button>
    </form>

    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modal-delete">
        <i class="fa fa-times-circle"></i>
        删除笔记
    </button>

    {{-- 确认删除 --}}
    <div class="modal fade" id="modal-delete" tabIndex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">提示</h4>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        <i class="fa fa-question-circle fa-lg"></i>
                        您确定要删除笔记{{ $list->title }}?
                    </p>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="{{ url('/biji/'.$list->id) }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-times-circle"></i> Yes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <a class="atip" data-toggle="tooltip" data-placement="bottom" title="笔记本" ><span class="glyphicon glyphicon-book" > </span></a> {{ $book->title }}
    <iframe style="float: right;" width="320" scrolling="no" height="60" frameborder="0" allowtransparency="true" src="http://i.tianqi.com/index.php?c=code&id=12&icon=1&num=5"></iframe>
</div>