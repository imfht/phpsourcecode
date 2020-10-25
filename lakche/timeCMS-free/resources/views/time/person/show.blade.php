@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid" id="person">
        <div class="container">
            <div class="row">
                <div class="col-sm-3 left">
                    <img src="{{ $person->head_thumbnail }}" alt="{{ $person->title }} {{ $person->name }}" class="head">
                    <h2>{{ $person->name }}</h2>
                    <h3>{{ $person->title }}</h3>
                    <p>{{ $person->info }}</p>
                    <div class="info clearfix">
                        <div class="col-xs-4"><span>{{ $person->sex ? '女' : '男' }}</span>性别</div>
                        <div class="col-xs-4"><span>{{ $person->age }}</span>从业</div>
                        <div class="col-xs-4"><span>{{ $person->point }}</span>贡献</div>
                        <br>
                    </div>
                    <div class="tag">
                        @if($person->tag != '[""]')
                            @foreach( json_decode($person->tag) as $tag )
                                <div><i class="glyphicon glyphicon-heart"></i>{{ $tag }}</div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-sm-9 right">
                    <h2 class="clearfix"><i class="glyphicon glyphicon-user"></i>{{ $person->name }} 个人简介</h2>
                        {!! $person->text !!}
                </div>
            </div>
        </div>
    </div>
    <script>
        $('.right').css('min-height',$('.left').height()+'px');
    </script>
@endsection