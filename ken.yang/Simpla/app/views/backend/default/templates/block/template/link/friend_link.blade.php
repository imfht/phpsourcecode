<ul class="list-group">
    @foreach($links as $link)
    <li><a href="{{$link['url']}}" target="_blank" title="{{$link['description']?$link['description']:$link['title']}}">{{$link['title']}}</a></li>
    @endforeach
</ul>