@extends('layouts._base')
@section('base_content')
    @parent
@include('common._nav', ['extraClass' => ''])
<div class="container t-main" id="gryenApp">
    @yield('content')
</div>
@include('errors._list')
@endsection
