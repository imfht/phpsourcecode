@extends('body')
@section('header')
<style>
.movie-table {
}
.block {
  display:block;
}
.file {
  width:100%;
  padding-left:20px;
  /*max-height: 41px;*/
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
small {
  float:right;
}
</style>
@endsection
@section('body')
    <div class="ali center">
        <div class="by">
            <h4 class="ty">
              @if(@$data['score'] > 0)
                <label class="score">{{ $data['score'] }}</label>
              @endif
                {{ $data['title'] }}
            </h4>
            <div class="ph">
              <img src="{{ $data['image'] }}">
            </div>
            <div class="ph movie-table">
              <span class="dh">类型</span>
                {{ $data['type'] }}
            </div>
            <div class="ph movie-table">
              <span class="dh">国家</span>
                {{ $data['country'] }}
            </div>

            <div class="ph movie-table">
              <span class="dh">年代</span>
                {{ $data['year'] }}
            </div>
            <div class="ph movie-table">
              @foreach($data['torrent'] as $index=>$torrent)
                <a href="/movie/{{ $data['id'] }}/{{ $index }}" class="block">{{ $torrent['file_name'] }} {{ $torrent['size'] }}</a>
                <!-- <label class="file">{{ $torrent['size'] }}</label> -->
              @endforeach
            </div>
            @if(isset($data['download']))
            <div class="ph movie-table">
              <span class="dh">提示</span>
              {{ $data['download'] }}
            </div>
          @endif
        </div>
    </div>
@endsection
