			<div id="main">
				<div class="nav"><a href="{U_INDEX}">首页</a>&gt;金币管理</div>
   			 	<div class="title">金币管理</div>
    			<form action="{S_ACTION}" method="post">
    				<div id="select-object">
						<label>对象：</label>
						<div>
							<select name="object">
								<option value="0">任意用户</option>
								<option value="2">所有版主</option>
								<option value="1">所有管理员</option>
							</select>							
						</div>
    				</div>
    				<div id="write-user-id">
 						<label>会员ID：</label>
                        <p>如果您选择的是所有版主、所有管理员不用输入此选项，为什么？？?因为输入也是无效。</p>
 						<input type="text" name="user_id" size="8" value="" />
 					</div>
  					<div id="write-number">
  						<label>数量：</label>
                        <p>请输入您要更改的金币数量</p>
  						<input type="text" name="number" size="11" value="" />
  					</div>
  					<div id="select-action">
  	  					<input type="radio" name="action" value="0" /> 增加 
    					<input type="radio" name="action" value="1" /> 减少
    				</div>
    				<input type="submit" name="submit" value="保存" />
    			</form>
    		</div>