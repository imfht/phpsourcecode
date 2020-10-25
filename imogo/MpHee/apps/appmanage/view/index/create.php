  <form enctype="multipart/form-data" method="post" action="{url('index/create')}">
     <div class="form_box">
        <table>
            <tr>
              <th>应用ID：</th>
              <td><input class="input w200" type="text" name="app_id" id="app_id" />(必须为全小写字母)</td>
            </tr>
            <tr>
              <th>应用名称：</th>
              <td><input class="input w200" type="text" name="app_name" id="app_name" /></td>
            </tr>
			<tr>
              <th>应用排序：</th>
              <td><input class="input w200" type="text" name="sort" id="sort" /></td>
            </tr>
        </table>
      </div>
   <div class="btn tac">
   <input type="submit" name="dosubmit" value="创 建" class="button">
   </div>
  </form>