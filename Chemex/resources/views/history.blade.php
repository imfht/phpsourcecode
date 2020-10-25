@if(count($data)>0)
    <table class="table">
        <thead>
        <tr>
            <th>状态</th>
            <th>事件</th>
            <th>时间</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $item)
            <tr>
                @if($item['status']=='+')
                    <td><i class="feather icon-plus" style="color:royalblue"></i></td>
                    <td>关联了{{$item['type'].' : '.$item['name']}}</td>
                @else
                    <td><i class="feather icon-minus" style="color:orangered"></i></td>
                    <td>解除了{{$item['type'].' : '.$item['name']}}</td>
                @endif
                <td>{{$item['datetime']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center;color: rgba(0,0,0,0.7)">无内容</div>
@endif
