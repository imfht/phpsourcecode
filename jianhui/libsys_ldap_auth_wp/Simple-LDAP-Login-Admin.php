<?php
global $SimpleLDAPLibsysLogin;

if( isset( $_GET[ 'tab' ] ) ) {
    $active_tab = $_GET[ 'tab' ];
} else {
	$active_tab = 'simple';
}
?>
<div class="wrap">

    <div id="icon-themes" class="icon32"></div>
    <h2>Simple LDAP Libsys Login Settings</h2>

    <h2 class="nav-tab-wrapper">
        <a href="<?php echo add_query_arg( array('tab' => 'simple'), $_SERVER['REQUEST_URI'] ); ?>" class="nav-tab <?php echo $active_tab == 'simple' ? 'nav-tab-active' : ''; ?>">Simple</a>
        <a href="<?php echo add_query_arg( array('tab' => 'advanced'), $_SERVER['REQUEST_URI'] ); ?>" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>">Advanced</a>
        <a href="<?php echo add_query_arg( array('tab' => 'libsys'), $_SERVER['REQUEST_URI'] ); ?>" class="nav-tab <?php echo $active_tab == 'libsys' ? 'nav-tab-active' : ''; ?>">Libsys</a>
        
        <a href="<?php echo add_query_arg( array('tab' => 'help'), $_SERVER['REQUEST_URI'] ); ?>" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
    </h2>

    <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    	<?php wp_nonce_field( 'save_sll_settings','save_the_sll' ); ?>

    	<?php if( $active_tab == "simple" ): ?>
    	<h3>Required</h3>
    	<p>These are the most basic settings you must configure. Without these, you won't be able to use Simple LDAP Login.</p>
    	<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">Enable LDAP Authentication</th>
					<td>
						<input type="hidden" name="<?php echo $this->get_field_name('enabled'); ?>" value="false" />
						<label><input type="checkbox" name="<?php echo $this->get_field_name('enabled'); ?>" value="true" <?php if( str_true($this->get_setting('enabled')) ) echo "checked"; ?> /> Enable LDAP login authentication for WordPress. (this one is kind of important)</label><br/>
					</td>
	    		<tr>
	    		<tr>
					<th scope="row" valign="top">Account Suffix</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('account_suffix'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('account_suffix'); ?>" /><br/>
						Often the suffix of your e-mail address. Example: @gmail.com
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Base DN</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('base_dn'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('base_dn'); ?>" />
						<br/>
						Example: For subdomain.domain.suffix, use DC=subdomain,DC=domain,DC=suffix. Do not specify an OU here.
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Domain Controller(s)</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('domain_controllers', 'array'); ?>" value="<?php echo join(';', (array)$SimpleLDAPLibsysLogin->get_setting('domain_controllers')); ?>" />
						<br/>Separate with semi-colons.
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">LDAP Directory</th>
					<td>
						<label><input type="radio" name="<?php echo $this->get_field_name('directory'); ?>" value="ad" <?php if( $this->get_setting('directory') == "ad" ) echo "checked"; ?> /> Active Directory</label><br/>
						<label><input type="radio" name="<?php echo $this->get_field_name('directory'); ?>" value="ol" <?php if( $this->get_setting('directory') == "ol" ) echo "checked"; ?> /> Open LDAP (and etc)</label>
					</td>
				</tr>
			</tbody>
    	</table>
    	<p><input class="button-primary" type="submit" value="Save Settings" /></p>
    	<?php elseif ( $active_tab == "advanced" ): ?>
    	<h3>Typical</h3>
		<p>These settings give you finer control over how logins work.</p>
    	<table class="form-table" style="margin-bottom: 20px;">
			<tbody>
				<tr>
					<th scope="row" valign="top">Required Groups</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('groups', 'array'); ?>" value="<?php echo join(';', (array)$SimpleLDAPLibsysLogin->get_setting('groups')); ?>" /><br/>
						The groups, if any, that authenticating LDAP users must belong to. <br/>
						Empty means no group required. Separate with semi-colons.
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">LDAP Exclusive</th>
					<td>
						<input type="hidden" name="<?php echo $this->get_field_name('high_security'); ?>" value="false" />
						<label><input type="checkbox" name="<?php echo $this->get_field_name('high_security'); ?>" value="true" <?php if( str_true($this->get_setting('high_security')) ) echo "checked"; ?> /> Force all logins to authenticate against LDAP. Do NOT fallback to default authentication for existing users.<br/>Formerly known as high security mode.</label><br/>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">User Creations</th>
					<td>
						<input type="hidden" name="<?php echo $this->get_field_name('create_users'); ?>" value="false" />
						<label><input type="checkbox" name="<?php echo $this->get_field_name('create_users'); ?>" value="true" <?php if( str_true($this->get_setting('create_users')) ) echo "checked"; ?> /> Create WordPress user for authenticated LDAP login with appropriate roles.</label><br/>
					</td>
	    		<tr>
					<th scope="row" valign="top">New User Role</th>
					<td>
						<select name="<?php echo $this->get_field_name('role'); ?>">
							<?php wp_dropdown_roles( strtolower($this->get_setting('role')) ); ?>
						</select>
					</td>
				</tr>
			</tbody>
    	</table>
    	<hr />
    	<h3>Extraordinary</h3>
    	<p>Most users should leave these alone.</p>
    	<table class="form-table">
			<tbody>
	    		<tr>
					<th scope="row" valign="top">LDAP Login Attribute</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('ol_login'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('ol_login'); ?>" />
						<br />
						In case your installation uses something other than <b>uid</b>; 
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">Use TLS</th>
					<td>
						<input type="hidden" name="<?php echo $this->get_field_name('use_tls'); ?>" value="false" />
						<label><input type="checkbox" name="<?php echo $this->get_field_name('use_tls'); ?>" value="true" <?php if( str_true($this->get_setting('use_tls')) ) echo "checked"; ?> /> Transport Layer Security. This feature is beta, very beta.</label><br/>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">LDAP Port</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('ldap_port'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('ldap_port'); ?>" /><br/>
						This is usually 389.
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">LDAP Version</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('ldap_version'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('ldap_version'); ?>" /><br/>
						Only applies to Open LDAP. Typically 3.
					</td>
				</tr>
			</tbody>
    	</table>
    	<p><input class="button-primary" type="submit" value="Save Settings" /></p>
    	<?php elseif( $active_tab == "libsys" ): ?>
    	<h3>必须</h3>
    	<p>是否启用汇文Libsys认证.</p>
    	<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" valign="top">启用Libsys认证</th>
					<td>
						<input type="hidden" name="<?php echo $this->get_field_name('libsys_enabled'); ?>" value="false" />
						<label><input type="checkbox" name="<?php echo $this->get_field_name('libsys_enabled'); ?>" value="true" <?php if( str_true($this->get_setting('libsys_enabled')) ) echo "checked"; ?> /> 使用WordPress的汇文Libsys读者认证. (this one is kind of important)</label><br/>
					</td>
	    		<tr>
	    		<tr>
					<th scope="row" valign="top">汇文Oracle服务器IP</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('libsys_server_ip'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('libsys_server_ip'); ?>" /><br/>
						汇文Oracle服务器IP
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">汇文Oracle服务器端口</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('libsys_server_port'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('libsys_server_port'); ?>" />
						<br/>
						汇文Oracle服务器端口号，通常为1521
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">汇文Oracle服务器用户名</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('libsys_server_user'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('libsys_server_user'); ?>" />
						
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">汇文Oracle服务器密码</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('libsys_server_passwd'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('libsys_server_passwd'); ?>" />
						
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">汇文Oracle服务器DB</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('libsys_server_db'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('libsys_server_db'); ?>" />
						
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">汇文查询条件</th>
					<td>
						<input type="text" name="<?php echo $this->get_field_name('libsys_server_query'); ?>" value="<?php echo $SimpleLDAPLibsysLogin->get_setting('libsys_server_query'); ?>" />
						<br/>数据库sql语句查询条件，默认为select * from reader where cert_id = :userid and password = :password
					</td>
				</tr>
				<tr>
					<th scope="row" valign="top">LDAP与汇文优先使用权</th>
					<td>
						<label><input type="radio" name="<?php echo $this->get_field_name('libsys_use_first'); ?>" value="first_ldap" <?php if( $this->get_setting('libsys_use_first') == "first_ldap" ) echo "checked"; ?> /> 优先使用LDAP</label><br/>
						<label><input type="radio" name="<?php echo $this->get_field_name('libsys_use_first'); ?>" value="first_libsys" <?php if( $this->get_setting('libsys_use_first') == "first_libsys" ) echo "checked"; ?> /> 优先使用汇文Libsys读者账号</label>
					</td>
				</tr>
			</tbody>
    	</table>
    	<p><input class="button-primary" type="submit" value="Save Settings" /></p>
    	<?php else: ?>
		<h3>Help</h3>
		<p>Here's a brief primer on how to effectively use and test Simple LDAP Login.</p>
		<h4>Testing</h4>
		<p>The most effective way to test logins is to use two browsers. In other words, keep WordPress Admin open in Chrome, and use Firefox to try logging in. This will give you real time feedback on your settings and prevent you from inadvertently locking yourself out.</p>
		<h4>Which raises the question, what happens if I get locked out?</h4>
		<p>If you accidentally lock yourself out, the easiest way to get back in is to rename <strong><?php echo plugin_dir_path(__FILE__); ?></strong> to something else and then refresh. WordPress will detect the change and disable Simple LDAP Login. You can then rename the folder back to its previous name.</p>
    	<?php endif; ?>
    </form>
</div>