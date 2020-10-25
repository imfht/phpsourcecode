@if($type==='submit')
    <button type="submit" class="btn {{$class}}">{{$label}}</button>
@else
    <a href="{{$url}}" class="btn {{$class}}">{{$label}}</a>
@endif