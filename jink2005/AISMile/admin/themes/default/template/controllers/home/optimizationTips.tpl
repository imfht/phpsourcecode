{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<div class="admin-box1">
<h5>{l s='Configuration checklist'}
	<span style="float:right">
	<a id="optimizationTipsFold" href="#">
		<img alt="v" src="../img/admin/{if $hide_tips}arrow-down.png{else}arrow-up.png{/if}" />
	</a>
	</span>
</h5>
			<ul id="list-optimization-tips" class="admin-home-box-list" {if $hide_tips}style="display:none"{/if} >
			{foreach from=$opti_list item=i key=k}
				<li>
				<img src="../img/admin/{$i.image}" class="pico" />
					<a  style="color:{$i.color}" href="{$i.href}">{$i.title}</a>
				</li>

			{/foreach}
			</ul>

</div>

<script type="text/javascript">
$(document).ready(function(){
	{if !$hide_tips}
		$("#optimizationTipsFold").click(function(e){
			e.preventDefault();
			$.ajax({
						url: "ajax-tab.php",
						type: "POST",
						data:{
							token: "{$token}",
							ajax: "1",
							controller : "AdminHome",
							action: "hideOptimizationTips"
						},
						dataType: "json",
						success: function(json){
							if(json.result == "ok")
								showSuccessMessage(json.msg);
							else
								showErrorMessage(json.msg);

						} ,
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{

						}
					});

		});
	{/if}
	$("#optimizationTipsFold").click(function(e){
		e.preventDefault();
		$("#list-optimization-tips").toggle(function(){
			if($("#optimizationTipsFold").children("img").attr("src") == "../img/admin/arrow-up.png")
				$("#optimizationTipsFold").children("img").attr("src","../img/admin/arrow-down.png");
			else
				$("#optimizationTipsFold").children("img").attr("src","../img/admin/arrow-up.png");
		});
	})
});
</script>
