  <div class="list_b">
    <table>
        <tr>
          <th class="w40">应用ID</th>
          <th>应用名称</th>
		  <th class="w30">排序</th>
		  <th class="w120">版本</th>
		  <th class="w80">开发者</th>
          <th class="w320">管理操作</th>
        </tr>
      {loop $apps $app $config}
        <tr>         
          <td>{$app}</td>
		  <td>{$config['APP_NAME']}</td>
		  <td>{$config['APP_SORT']}</td>
		  <td>{$config['APP_VER']}</td>
		  <td>{$config['APP_AUTHOR']}</td>
          <td>
            {if $config['APP_STATE'] == 1}
              <a href="{url('index/export', array('app'=>$app))}" class="button"><i class="fa fa-upload"></i> 导出</a>
              <a href="{url('index/uninstall', array('app'=>$app))}" onclick="return confirm('将会删除所有数据表和文件,确定要删除吗？')" class="button"><span class="red"><i class="fa fa-trash-o"></i> 删除</span></a>
              <a href="{url('index/state', array('app'=>$app,'state'=>2))}" class="button"><span class="red"><i class="fa fa-power-off"></i> 停用</span></a> 
              {if $app==DEFAULT_APP}
			  <a class="button"><span class="green"><i class="fa fa-ban"></i> 已为默认</span></a>
			  {else}
			  <a href="{url('index/setdefault', array('app'=>$app))}" class="button"><span class="red"><i class="fa fa-pencil"></i> 设为默认</span></a>
			  {/if}
			{elseif $config['APP_STATE'] == 2}
              <a href="{url('index/export', array('app'=>$app))}" class="button"><i class="fa fa-upload"></i> 导出</a>
              <a href="{url('index/uninstall', array('app'=>$app))}" onclick="return confirm('将会删除所有数据表和文件,确定要删除吗？')" class="button"><span class="red"><i class="fa fa-trash-o"></i> 删除</span></a>
              <a href="{url('index/state', array('app'=>$app,'state'=>1))}" class="button"><span class="green"><i class="fa fa-play-circle"></i> 启用</span></a>            
			{else}
              <a href="{url('index/install', array('app'=>$app))}" class="button"><span class="green">安装</span></a>  
			{/if}
        </td>
       </tr> 
       {/loop} 
    </table>
	</div>
	<div class="t-pages mt10 right">{$page}</div>