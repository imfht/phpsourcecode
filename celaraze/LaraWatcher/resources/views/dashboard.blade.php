<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/mdui@1.0.0/dist/css/mdui.min.css"
    integrity="sha384-2PJ2u4NYg6jCNNpv3i1hK9AoAqODy6CdiC+gYiL2DVx+ku5wzJMFNdE3RoWfBIRP"
    crossorigin="anonymous"
/>
<script
    src="https://cdn.jsdelivr.net/npm/mdui@1.0.0/dist/js/mdui.min.js"
    integrity="sha384-aB8rnkAu/GBsQ1q6dwTySnlrrbhqDwrDnpVHR2Wgm8pWLbwUnzDcIROX3VvCbaK+"
    crossorigin="anonymous"
></script>
<div class="mdui-container">
    <table class="mdui-table">
        <thead>
        <tr>
            <th scope="col">服务器</th>
            <th scope="col">服务</th>
            <th scope="col">状态</th>
            <th scope="col">问题</th>
            <th scope="col">预计/修复时间</th>
        </tr>
        </thead>
        <tbody>
        @foreach($services as $service)
            <tr>
                <td>{{$service['server']['name']}}</td>
                <td>{{$service['name']}}</td>
                <td>
                    @if($service['status'] == 0 || $service['status'] == 2)
                        🟢
                    @endif
                    @if($service['status'] == 1)
                        🔴
                    @endif
                </td>
                <td>
                    @if($service['status'] == 2)
                        <span style="color: #00b44e;font-weight: 600">[已修复最近一次的问题]</span> {{$service['issue']}}
                    @else
                        {{$service['issue']}}
                    @endif
                </td>
                <td>{{$service['recovery']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
