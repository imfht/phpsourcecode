<h4>硬件</h4>
@if(count($data['hardware'])>0)
    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>分类</th>
            <th>名称</th>
            <th>规格</th>
            <th>序列号</th>
            <th>制造商</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data['hardware'] as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->category->name}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->specification}}</td>
                <td>{{$item->sn}}</td>
                <td>{{$item->vendor->name}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center;color: rgba(0,0,0,0.7)">无内容</div>
@endif
<h4 style="margin-top: 20px;">软件</h4>
@if(count($data['software'])>0)
    <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>分类</th>
            <th>名称</th>
            <th>版本</th>
            <th>授权方式</th>
            <th>制造商</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data['software'] as $item)
            <tr>
                <td>{{$item->id}}</td>
                <td>{{$item->category->name}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->version}}</td>
                <td>{{$item->distribution}}</td>
                <td>{{$item->vendor->name}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div style="text-align: center;color: rgba(0,0,0,0.7)">无内容</div>
@endif
