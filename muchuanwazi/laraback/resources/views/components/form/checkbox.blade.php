<div class="form-group">
    @foreach($items as $key=>$item)
        <div class="checkbox">
            <label>
        {{ Form::checkbox($item['name'],$item['value'],$item['isCheck'],$attributes)}}
           {{$item['label']}} </label>
        </div>
    @endforeach

</div>