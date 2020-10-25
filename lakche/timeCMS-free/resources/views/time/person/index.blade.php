@extends($theme.'.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-sm-9">
                        <div class="panel panel-primary">
                            <div class="panel-heading">荣誉殿堂</div>
                            <div class="panel-body" id="persons">
                                <div class="row">
                                    @if($persons = Theme::person_data(999,byPoint))
                                    @foreach($persons as $person)
                                        <div class="col-sm-6 col-md-4">
                                            <div class="thumbnail">
                                                <div class="pic">
                                                    <p><a href="{{ url('person',$person->id) }}"><img src="{{ $person->getHead() }}" alt="{{ $person->name }}"></a></p>
                                                    <span><s></s><div>{{ $person->point }}</div></span>
                                                </div>
                                                <div class="caption text-center">
                                                    <a href="{{ url('person',$person->id) }}"><h3>{{ $person->name }}</h3></a>
                                                    <p>{{ $person->title == '' ? '　' : $person->title }}</p>
                                                    <p>{{ $person->info == '' ? '　' : str_limit($person->info,20,'...') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @endif
                                </div>

                            </div>
                        </div>
                </div>
                <div class="col-sm-3">
                    @include($theme.'.category.right')
                </div>
            </div>
        </div>
    </div>
@endsection