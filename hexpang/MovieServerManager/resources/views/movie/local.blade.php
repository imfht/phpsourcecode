@extends('body')

@section('body')
  @if($data['movies'])
  <div class="ud">
    <div class="eg">
      <table class="cl" data-sort="table">
        <thead>
          <tr>
            <th class="header">文件名</th>
            {{-- <th class="header">大小</th> --}}
            <th class="header">操作</th>
          </tr>
        </thead>
        <tbody>
          {{-- <td>{{ $movie['size'] }}</td> --}}
          @foreach($data['movies'] as $movie)
            <tr><td>{{ $movie['name'] }}</td><td><a href="/movie/local/{{ base64_encode($movie['file']) }}">播放</a></td></tr>
          @endforeach
      </tbody>
      </table>
    </div>
  </div>
  @else
    <label>没有文件</label>
  @endif
@endsection
