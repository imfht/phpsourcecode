<div class="panel panel-default new_user_list">
    @if($block['title'])
    <div class="panel-heading">
        {{$block['title']}}
    </div>
    @endif
    <div class="panel-body">
        <div class="row">
            @foreach($users as $user)
            <div class="col-md-3 col-sm-3 col-xs-6">
                <a href="/user/{{$user['id']}}" class="thumbnail">
                    <img class="img-responsive" src="/{{$user['picture']}}" alt="{{$user['username']}}" title="{{$user['username']}}"/>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
<style>
    .new_user_list .col-md-3,.new_user_list .col-sm-3,.new_user_list .col-xs-6{
        padding:0 5px;
    }
    .new_user_list .thumbnail{
        margin-bottom:5px;
    }
</style>