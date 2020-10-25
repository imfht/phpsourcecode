@foreach($users as $user)
<div><a href="/user/{{$user['id']}}">{{$user['username']}}</a></div>
@endforeach
