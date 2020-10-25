<table id="{{$table_id}}" class="table table-hover">
    <thead>
        <tr>
            <th>短链接</th>
            <th>原地址</th>
            <th>点击数</th>
            <th>创建时间</th>
            @if ($table_id == "admin_links_table")
            {{-- Show action buttons only if admin view --}}
            <th>所有者</th>
            <th>禁用</th>
            <th>删除</th>
            @endif
        </tr>
    </thead>
</table>
