@foreach($menu_top as $menu)
    @if(!isset($menu['child']))
        <li><a href="{{$menu['url']}}">{{$menu['title']}}</a></li>
    @else
        <li class="dropdown" role="presentation">
            <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                {{$menu['title']}} <span class="caret"></span>
            </a>
            <ul role="menu" class="dropdown-menu">
                @foreach($menu['child'] as $item)
                    <li><a href="{{$item['url']}}">{{$item['title']}}</a></li>
                @endforeach
            </ul>
        </li>
    @endif
@endforeach