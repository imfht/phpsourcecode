
<ul class="list-group">
    @foreach($nodes as $node)
    <li><a href="/node/{{$node['id']}}">{{$node['title']}}</a></li>
    @endforeach
</ul>
