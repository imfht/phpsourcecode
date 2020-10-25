<div style="width:338px; text-align:left;">
  <%If ClassID<>"" Then%>
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="box" style="margin-bottom:8px;">
    <tr>
      <td style="padding:8px;"><%Call Ok3w_DisNextClass(ChannelID,ClassID)%></td>
    </tr>
  </table>
  <%End If%>
  <table width="100%" border="0" cellpadding="0" cellspacing="0" class="box">
    <tr>
      <td><%=GetAdSense(6)%></td>
    </tr>
  </table>
  <table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
    <tr>
      <td class="head"><h3 class="L"></h3>
          <span class="TAG">最新下载</span></td>
    </tr>
    <tr>
      <td class="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
        <tr>
          <td><%Call Ok3w_Soft_List(ClassID,15,1,20,False,False,False,0,False,"new")%></td>
        </tr>
      </table></td>
    </tr>
  </table>
  <table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
    <tr>
      <td class="head"><h3 class="L"></h3>
          <span class="TAG">赞助商链接</span></td>
    </tr>
    <tr>
      <td style="border:1px solid #CCC;"><%=GetAdSense(7)%></td>
    </tr>
  </table>
  <table border="0" cellspacing="0" cellpadding="0" class="dragTable" width="100%">
    <tr>
      <td class="head"><h3 class="L"></h3>
          <span class="TAG">热门下载</span></td>
    </tr>
    <tr>
      <td class="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:5px 0px 5px 0px;">
        <tr>
          <td><%Call Ok3w_Soft_List(ClassID,15,1,20,False,False,False,0,False,"hot")%></td>
        </tr>
      </table></td>
    </tr>
  </table>
</div>
