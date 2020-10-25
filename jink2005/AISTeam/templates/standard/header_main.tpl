<div id="sitebody">

	<div id="header-wrapper">
		<div id="header">
			<div class="header-in">

				<div class="left">
					<div class="logo">
						<h1>
							<a href="index.php" title="{#desktop#}"><img src="./templates/standard/images/logo-b.png" alt="" /></a>
							<span class="title">{$settings.name}<span class="subtitle">{if $settings.subtitle}/ {$settings.subtitle} {/if}</span></span>
						</h1>
					</div>

				</div> {*left End*}

				<div class="right">
				<div class="menu">
					{if $loggedin == 1}
						<ul >
							
							<li ><a href="manageuser.php?action=profile&amp;id={$userid}">{#myaccount#}</a></li>

							 {if $userpermissions.admin.add || $userpermissions.user.add || $userpermissions.user.edit || $userpermissions.user.del}
							<li ><a href="admin.php?action=projects">{#administration#}</a>
								
									<ul>
									{if $userpermissions.admin.add}
										<li><a href="admin.php?action=projects">{#projectadministration#}</a></li>
									{/if}
									{if $userpermissions.user.add || $userpermissions.user.edit || $userpermissions.user.del}
										<li><a href="admin.php?action=users">{#useradministration#}</a></li>
									{/if}
									{if $userpermissions.admin.add}
										<li><a href="admin.php?action=system">{#systemadministration#}</a></li>
									{/if}
									</ul>
								
							</li>
							{/if}


							<li ><a href="manageuser.php?action=logout">{#logout#}</a></li>
						</ul>
					{/if}
					</div>
				</div> <!-- Right End -->



			</div> <!-- Header-In End -->
		</div> <!-- Header End -->
	</div> <!-- Header-Wrapper End -->


	<div id="contentwrapper">

