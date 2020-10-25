@if($errors->any())
    <ul class="list-group">
        @foreach($errors->all() as $error)
            <li class="list-group-item list-group-item-danger">{{$error}}</li>
        @endforeach
    </ul>
@endif