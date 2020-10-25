{include file="header.tpl" title="install" showheader="no"}


		<div class="install" style="text-align:center;padding:5% 0 0 0;">
			<div style="text-align:left;width:500px;margin:0 auto;padding:25px 25px 15px 25px;background:white;border:1px solid;">


			<h1>{#install2planteam#}</h1>

			<div style="padding:16px 0 16px 0;">
			<h2>{#installstep#} 3</h2>
            <em>{#createadmin#}</em><br /><br />


			<form class = "main" name = "adminuser" method = "post" enctype="multipart/form-data" action = "install.php?action=step3">
			<fieldset>
			<div class = "row"><label for = "username">{#name#}:</label><input type = "text" name = "name" id = "username" /></div>
			<div class = "row"><label for = "pass">{#password#}:</label><input type = "password" name = "pass" id = "pass" /></div>

			</fieldset>
			<br />
			<!--
			<h2>{#import#}</h2>
			<fieldset>

			<div class="row">
						<label for="file">{#file#}:</label>
						<div class="fileinput" >
								<input type="file" class="file" name="importfile" id="importfile"  realname="{#file#}" size="26" onchange = "file.value = this.value;" />
								<table class = "faux" cellpadding="0" cellspacing="0" border="0" style="padding:0;margin:0;border:none;">
									<tr>
									<td><input type="text" class="text-file" name = "userfile1" id="file" required="1" realname="{#file#}"></td>
									<td class="choose"><button class="inner" onclick="return false;">{#chooseone#}</button></td>
									</tr>
								</table>
						</div>
					</div>

			<div style="border-bottom:1px dashed;height:16px;display:block;clear:both;margin-bottom:16px;"></div>
			-->
			<div class="row-butn-bottom">
				<label>&nbsp;</label>
				<button type="submit"  onfocus="this.blur();">{#continue#}</button>
			</div>
			</fieldset>
			</form>

			</div>
			</div>
		</div> {*Install end*}



</body>
</html>
