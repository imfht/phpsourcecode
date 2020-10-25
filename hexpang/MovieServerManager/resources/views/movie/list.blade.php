@extends('body')
@section('right_section')
  <div class="ob ape">
  <div class="tn aol">
  <div class="aor">
  <input class="form-control" id="movie_id" type="text" placeholder="请输入电影名称(英文名)...">
  <button type="button" class="fm" onclick="location.href='/movie/search/' + $('#movie_id').val();">
  <span class="bv adn"></span>
  </button>
  </div>
  </div>
  </div>
@endsection
@section('header')
  <style>
  .movie-block {
    position: relative;
    padding: 10px 15px;
    margin-bottom: -1px;
    background-color: transparent;
    border: 1px solid #434857;
    height:165px;
  }
  .movie-image {
    float:left;
    width:25%;
  }
  .movie-summery {
    float:right;
    width:70%;
    min-width: 55%;
    max-width: 70%;
  }
  .movie-table {
    max-height: 48px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .ph label {
    margin-left:5px;
  }
  .title {
    max-height: 41px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .movie-filter {
    margin-bottom:5px;
  }
  </style>
@endsection
@section('body')
  <div class="ph movie-filter">
    @foreach($data['movie_type'] as $key=>$name)
      <a href="/movie/list/{{ $data['page'] }}/{{ $key }}">{{ $name }}</a>
    @endforeach
  </div>
  @if(count($data['movies']) > 0)
    <div class="fu">
      @foreach($data['movies'] as $movie)
        <div class="gr">
            <div class="by">
                <a class="ty title" href="/movie/{{ $movie['id'] }}">
                  @if(@$movie['score'] > 0)
                    <label class="score">{{ $movie['score'] }}</label>
                  @endif
                    {{ $movie['title'] }}
                </a>
                <div class="movie-block">
                    <div class="movie-image">
                      <img src="{{ $movie['image'] }}" height="144" width="100">
                    </div>
                    <div class="movie-summery">
                      <div class="ph movie-table">
                        <span class="dh">类型</span>
                        {{ $movie['type'] }}
                      </div>

                      <div class="ph movie-table">
                        <span class="dh">国家</span>
                        {{ $movie['country'] }}
                      </div>

                      <div class="ph movie-table">
                        <span class="dh">年代</span>
                        {{ $movie['year'] }}
                      </div>
                    </div>
                </div>
            </div>
        </div>
      @endforeach
  </div>
  <div class="db">
  <ul class="ow">
    @if($param != null && $param != 1 && is_numeric($param))
    <li>
      <a href="/movie/list/{{ $param-1 }}/{{ $param1 ? $param1 : -1 }}" aria-label="Previous">
        <span aria-hidden="true">«</span>
      </a>
    </li>
    @endif
    @if(isset($data['page_range']))
    @for($i=$data['page_range'][0];$i<=$data['page_range'][1];$i++)
      <li @if($data['page'] == $i) class='active' @endif><a href="/movie/list/{{ $i }}/{{ $param1 ? $param1 : -1 }}">{{ $i }}</a></li>
    @endfor
    <li>
      <a href="/movie/list/{{ ($param ? $param : 1) + 1 }}/{{ $param1 ? $param1 : -1 }}" aria-label="Next">
        <span aria-hidden="true">»</span>
      </a>
    </li>
    @endif
  </ul>
</div>
@endif
@endsection
