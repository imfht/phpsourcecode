<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <form id="form1" name="form1" method="post" action="./index.php?p=<?php echo $request['p'] ?>&n=<?php echo $request['n'] ?>&a=edit">
    <tr class="adtbtitle">
      <td><h3>表单处理：</h3>
        <a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="91"><div align="right">
          <input name="submit" type="submit" value=" 保存 " class="savebt" />
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="180">表单标题</td>
            <td ><?php echo $order_item->title?></td>
          </tr>
          <?php sys_push($order_item->custom,'<tr>
            <td width="180">{name}</td>
            <td >{value}</td>
          </tr>',0);?>
          <tr>
            <td width="180">备注</td>
            <td ><?php echo $order_item->remark?></td>
          </tr>
          <tr width="57">
            <td><strong>处理结果</strong></td>
            <td><label>
                <textarea name="result" id="result"><?php echo $order_item->result ?></textarea>
              </label></td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
