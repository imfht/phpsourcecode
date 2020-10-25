  <form enctype="multipart/form-data" method="post" action="{url('index/import')}">
    <div class="form_box">
        <table>
            <tr>
              <th>应用安装包：</th>
              <td><input class="inputfile w400" type="file" name="file" id="file" /></td>
            </tr>
        </table>
      </div>
      <div class="btn tac">
      <input type="submit" value="确 定" class="button">
      <input type="reset" value="重 置" class="button">
      </div>
  </form>