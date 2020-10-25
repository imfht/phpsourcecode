@foreach($menu_bottom as $menu)
    <a class="btn btn-link" href="{{$menu['url']}}">{{$menu['title']}}</a>  
@endforeach