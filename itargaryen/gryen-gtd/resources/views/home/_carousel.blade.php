<div class="col-md-8">
    <div id="carouselExampleIndicators" class="carousel slide t-index-carousel" data-ride="carousel">
        <ol class="carousel-indicators">
            @foreach($banners as $banner)
                <li data-target="#carouselExampleIndicators" data-slide-to="{{ $loop->index }}"
                    @if ($loop->first) class="active" @endif></li>
            @endforeach
        </ol>
        <div class="carousel-inner">
            @foreach($banners as $banner)
                <div class="carousel-item @if ($loop->first) active @endif">
                    <a href="{{$banner->href}}">
                        <img class="d-block w-100" src="{{ imageView2($banner->cover, ['w' => 960,'h' => 540]) }}"
                            alt="First slide">
                        <div class="t-index-carousel-title">
                            <h6 class="mb-1 mt-3 text-dark">{{ $banner->articleTitle }}</h6>
</div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    <top-articles></top-articles>
</div>
