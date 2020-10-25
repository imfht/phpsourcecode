<div class="install_right">
  <div class="install_box">
        <div class="list_t">
			<table width="100%">
                  <tr>
                    <th class="w200">检查项目</th>
                    <th>当前环境</th>
                    <th class="w140">系统要求</th>
                    <th class="w40">状态</th>
                  </tr>
                  <tr>
                    <td>web 服务器</td>
                    <td>{$_SERVER['SERVER_SOFTWARE']}</td>
                    <td>Apache/nginx/IIS等</td>
                    <td><span class="green">√</span></td>
                  </tr>
                  <tr>
                    <td>php 版本</td>
                    <td>php {PHP_VERSION}</td>
                    <td>php 5.0.0 及以上</td>
                    <td>{if $ifVer} {$yes} {else} {$no}{/if}</td>
                  </tr>
                  <tr>
                    <td>mysql数据库</td>
                    <td>{if $ifMysql}已支持{else}不支持{/if}</td>
                    <td>必须支持</td>
                    <td>{if $ifMysql} {$yes} {else} {$no}{/if}</td>
                  </tr>
                  <tr>
                    <td>gd 扩展</td>
                    <td>{if $ifGd}已开启{else}未开启{/if}</td>
                    <td>必须开启</td>
                    <td>{if $ifGd} {$yes} {else} {$no}{/if}</td>
                  </tr>
				  {loop $rwFiles $file $status}
                  <tr>
                    <td>{$file}</td>
                    <td>{if $status}可写{else}不能写{/if}</td>
                    <td>必须可写</td>
                    <td>{if $status} {$yes} {else} {$no}{/if}</td>
                  </tr>
				  {/loop}
             </table>
             </div>
  </div>
<div class="btn tac"> <input class="button" value="上一步" type="button" onClick="window.location.href = '{url('index/index')}'"> <input class="button" value="下一步" type="button" onClick="window.location.href = '{url('index/db')}'"></div>
</div>