<?php
/*
Plugin Name: Simple LDAP Login, Zhangyc niit
Plugin URI: http://www.vpn358.com/simple-ldap-login/
Description:  Authenticate WordPress against LDAP.Modified by zhangyc for niit
Version: 3.0
Author: Zhangyc.
Author URI: http://www.vpn358.com
*/

class SimpleLDAPLibsysLogin {
	static $instance = false;
	var $prefix = 'sll_';
	var $settings = array();
	var $adldap;
	var $ldap;

	public function __construct () {
		$this->settings = $this->get_settings_obj( $this->prefix );

		if( $this->get_setting('directory') == "ad" ) {
			require_once( plugin_dir_path(__FILE__) . "/includes/adLDAP.php" );
			$this->adldap = new adLDAP(
				array (
					"account_suffix"		=>	$this->get_setting('account_suffix'),
					"use_tls"				=>	str_true( $this->get_setting('use_tls') ),
					"base_dn"				=>	$this->get_setting('base_dn'),
					"domain_controllers"	=>	(array)$this->get_setting('domain_controllers'),
					"ad_port"				=>	$this->get_setting('ldap_port')
				)
			);
		}
		if( str_true($this->get_setting('libsys_enabled')) ) {
			require_once( plugin_dir_path(__FILE__) . "/includes/libsys_oracle.php" );

		}
		add_action('admin_init', array($this, 'save_settings') );
		add_action('admin_menu', array($this, 'menu') );

		if ( str_true($this->get_setting('enabled')) || str_true($this->get_setting('libsys_enabled')) ) {
			add_filter('authenticate', array($this, 'authenticate'), 1, 3);
		}

		register_activation_hook( __FILE__, array($this, 'activate') );

		// If version is false, and old version detected, run activation
		if( $this->get_setting('version') === false || get_option('simpleldap_domain_controllers', false) !== false ) $this->activate();
	}

	public static function getInstance () {
		if ( !self::$instance ) {
		  self::$instance = new self;
		}
		return self::$instance;
	}

	function activate () {
		// Default settings
		$this->add_setting('account_suffix', "@mydomain.org");
		$this->add_setting('base_dn', "DC=mydomain,DC=org");
		$this->add_setting('domain_controllers', array("dc01.mydomain.local") );
		$this->add_setting('directory', "ad");
		$this->add_setting('role', "Contributor");
		$this->add_setting('high_security', "true");
		$this->add_setting('ol_login', "uid");
		$this->add_setting('use_tls', "false");
		$this->add_setting('ldap_port', 389);
		$this->add_setting('ldap_version', 3);
		$this->add_setting('create_users', "false");
		$this->add_setting('enabled', "false");



		if( $this->get_setting('version') === false ) {
			$this->set_setting('version', '1.5');
			$this->set_setting('enabled', 'true');

			if ( $this->set_setting('account_suffix', get_option('simpleldap_account_suffix')) ) {
				//delete_option('simpleldap_account_suffix');
			}

			if ( $this->set_setting('base_dn', get_option('simpleldap_base_dn')) ) {
				//delete_option('simpleldap_base_dn');
			}

			if ( $this->set_setting('domain_controllers', get_option('simpleldap_domain_controllers')) ) {
				//delete_option('simpleldap_domain_controllers');
			}

			$directory_result = false;
			if ( get_option('simpleldap_directory_type') == "directory_ad" ) {
				$directory_result = $this->set_setting('directory', 'ad');
			} else {
				$directory_result = $this->set_setting('directory', 'ol');
			}

			//if( $directory_result ) delete_option('simpleldap_directory_type');
			unset($directory_result);

			if ( $this->set_setting('groups', (array)get_option('simpleldap_group') ) ) {
				//delete_option('simpleldap_group');
			}

			if ( $this->set_setting('role', get_option('simpleldap_account_type')) ) {
				//delete_option('simpleldap_account_type');
			}

			if ( $this->set_setting('ol_login', get_option('simpleldap_ol_login')) ) {
				//delete_option('simpleldap_ol_login');
			}

			if ( $this->set_setting('use_tls', str_true( get_option('simpleldap_use_tls') ) ) ) {
				//delete_option('simpleldap_use_tls');
			}

			$create_users = false;
			if ( get_option('simpleldap_login_mode') == "mode_create_all" || get_option('simpleldap_login_mode') == "mode_create_group" ) {
				$create_users = true;
			}
			if ( $this->set_setting('create_users', $create_users) ) {
				//delete_option('simpleldap_login_mode');
			}

			$high_security = false;
			if ( get_option('simpleldap_security_mode') == "security_high" ) {
				$high_security = true;
			}
			if ( $this->set_setting('high_security', $high_security) ) {
				//delete_option('simpleldap_security_mode');
			}
 		}

 		//libsys settings
		$this->add_setting('libsys_enabled', "false");
		$this->add_setting('libsys_server_ip', "");
		$this->add_setting('libsys_server_port', 1521);
		$this->add_setting('libsys_server_user', "");
		$this->add_setting('libsys_server_passwd', "");
		$this->add_setting('libsys_server_db', "orcl");
		$this->add_setting('libsys_server_query', "");
		$this->add_setting('libsys_use_first', "libsys_first");
	}

	function menu () {
		add_options_page("Libsys LDAP Login", "Libsys LDAP Login", 'manage_options', "libsys-ldap-login", array($this, 'admin_page') );
	}

	function admin_page () {
		include 'Simple-LDAP-Login-Admin.php';
	}

	function get_settings_obj () {
		return get_option("{$this->prefix}settings", false);
	}

	function set_settings_obj ( $newobj ) {
		return update_option("{$this->prefix}settings", $newobj);
	}

	function set_setting ( $option = false, $newvalue ) {
		if( $option === false ) return false;

		$this->settings = $this->get_settings_obj($this->prefix);
		$this->settings[$option] = $newvalue;
		return $this->set_settings_obj($this->settings);
	}

	function get_setting ( $option = false ) {
		if($option === false || ! isset($this->settings[$option]) ) return false;

		return apply_filters($this->prefix . 'get_setting', $this->settings[$option], $option);
	}

	function add_setting ( $option = false, $newvalue ) {
		if($option === false ) return false;

		if ( ! isset($this->settings[$option]) ) {
			return $this->set_setting($option, $newvalue);
		} else return false;
	}

	function get_field_name($setting, $type = 'string') {
		return "{$this->prefix}setting[$setting][$type]";
	}

	function save_settings()
	{
		if( isset($_REQUEST["{$this->prefix}setting"]) && check_admin_referer('save_sll_settings','save_the_sll') ) {
			$new_settings = $_REQUEST["{$this->prefix}setting"];

			foreach( $new_settings as $setting_name => $setting_value  ) {
				foreach( $setting_value as $type => $value ) {
					if( $type == "array" ) {
						$this->set_setting($setting_name, explode(";", $value));
					} else {
						$this->set_setting($setting_name, $value);
					}
				}
			}

			add_action('admin_notices', array($this, 'saved_admin_notice') );
		}
	}

	function saved_admin_notice(){
	    echo '<div class="updated">
	       <p>Libsys LDAP Login settings have been saved.</p>
	    </div>';

	    if( ! str_true($this->get_setting('enabled')) ) {
			echo '<div class="error">
				<p> LDAP Login is disabled.</p>
			</div>';
	    }
	    if( ! str_true($this->get_setting('libsys_enabled')) ) {
			echo '<div class="error">
				<p> Libsys Login is disabled.</p>
			</div>';
	    }
	}

	function authenticate ($user, $username, $password) {
		// If previous authentication succeeded, respect that
		if ( is_a($user, 'WP_User') ) { return $user; }
		
		// Determine if user a local admin
		$local_admin = false;
		$user_obj = get_user_by('login', $username); 
		if( user_can($user_obj, 'update_core') ) $local_admin = true;
		
		if ( empty($username) || empty($password) ) {
			$error = new WP_Error();

			if ( empty($username) )
				$error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));

			if ( empty($password) )
				$error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));

			return $error;
		}
		
		// If high security mode is enabled, remove default WP authentication hook
		if ( str_true( $this->get_setting('high_security') ) && ! $local_admin ) {
			remove_filter('authenticate', 'wp_authenticate_username_password', 20, 3);
		}

		if (str_true($this->get_setting('enabled'))) {
				// Sweet, let's try to authenticate our user and pass against LDAP
				$auth_result = $this->ldap_auth($username, $password, $this->get_setting('directory') );
		
				if( $auth_result ) {
					// Authenticated, does user have required groups, if any?
					if( $this->user_has_groups( $username, $this->get_setting('directory') ) ) {
		
						$user = get_user_by('login', $username);
		
						if ( ! $user || ( strtolower($user->user_login) !== strtolower($username) ) )  {
							
							if(  str_true($this->get_setting('create_users')) ) {
								//do_action( 'wp_login_failed', $username );
								//return new WP_Error('invalid_username', __('<strong>Simple LDAP Login Error</strong>: LDAP credentials are correct, but there is no matching WordPress user and user creation is not enabled.'));
							
							
								$new_user = wp_insert_user( $this->get_user_data( $username, $this->get_setting('directory') ) );
			
								if( ! is_wp_error($new_user) )
								{
									// Successful Login
									$new_user = new WP_User($new_user);
									do_action_ref_array($this->prefix . 'auth_success', array($new_user) );
			
									return $new_user;
								}
								else
								{
									//do_action( 'wp_login_failed', $username );
									//return new WP_Error("{$this->prefix}login_error", __('<strong>Simple LDAP Login Error</strong>: LDAP credentials are correct and user creation is allowed but an error occurred creating the user in WordPress. Actual error: '.$new_user->get_error_message() ));
								}
							}
		
						} else {
							return new WP_User($user->ID);
						}
					} else {
						//return new WP_Error("{$this->prefix}login_error", __('<strong>Simple LDAP Login Error</strong>: Your LDAP credentials are correct, but you are not in an authorized LDAP group.'));
					}
		
				} 
				elseif ( str_true($this->get_setting('high_security')) ) {
					//return new WP_Error('invalid_username', __('<strong>Simple LDAP Login</strong>: Simple LDAP Login could not authenticate your credentials. The security settings do not permit trying the WordPress user database as a fallback.'));
				}
		}
		if (str_true($this->get_setting('libsys_enabled'))) {
				//libsys auth here 
				$orc_user= $this->get_setting('libsys_server_user');
				$orc_pw= $this->get_setting('libsys_server_passwd');
				$orc_db= $this->get_setting('libsys_server_db');
				$orc_ip= $this->get_setting('libsys_server_ip');
				$orc_port= $this->get_setting('libsys_server_port');
				//$orc_url="(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST={$orc_ip})(PORT = {$orc_port}))(CONNECT_DATA = (SID={$orc_db})))";
				$orc_url="{$orc_ip}:{$orc_port}/$orc_db";
				echo $orc_url;
				//$dbpara['oracle']['url'] = '210.28.96.50:1531/libsys.lib';
				//$orc_url = "{}"
				$orc_conn =new OraDBConnection($orc_user,$orc_pw,$orc_url);
				$sql = 'select cert_id,name,sex,dept,occupation,duty,position,education,code01,r_dep_id,dep_name from reader left join department on r_dep_id = dep_id where  reader.cert_id = :userid and (password = :password or password = :md5password) ';		
			    $statement = $orc_conn->prepare($sql);
			    $statement->bindParam(':userid', $username);
                $statement->bindParam(':password', $password);
                $statement->bindParam(':md5password', strtoupper(md5($password)));
			    $statement->execute();
				$readers=array();
				while (($row = $statement->fetchObject() )) {
					$reader["name"]=$row->name;
					$reader["sex"]=$row->sex;
					$reader["dept"]=$row->dept;
					$reader["occupation"]=$row->occupation;
					$reader["duty"]=$row->duty;
					$reader["position"]=$row->position;
			        $reader["education"]=$row->education;
			        $reader["code01"]=$row->code01;
			        $reader["dep_name"]=$row->dep_name;
					$readers[$row->cert_id]=$reader;

					// Libsys Authenticated, does user have required groups, if any?
					//if( $this->user_has_groups( $username, $this->get_setting('directory') ) ) {
					// user_has_groups代码不懂。。。
					
					if( true ) {
						$user = get_user_by('login', $username);
		
						if ( ! $user || ( strtolower($user->user_login) !== strtolower($username) ) )  {
							if( ! str_true($this->get_setting('create_users')) ) {
								do_action( 'wp_login_failed', $username );
								return new WP_Error('invalid_username', __('<strong>LDAP and Libsys Login Error</strong>: LDAP credentials are correct, but there is no matching WordPress user and user creation is not enabled.'));
							}
		
							//$new_user = wp_insert_user( $this->get_user_data( $username, $this->get_setting('directory') ) );
							

							$new_user = wp_insert_user( $this->get_libsys_user_data( $username,$reader));
							if( ! is_wp_error($new_user) )
							{
								// Successful Login
								$new_user = new WP_User($new_user);
								do_action_ref_array($this->prefix . 'libsys_auth_success', array($new_user) );
		
								return $new_user;
							}
							else
							{
								do_action( 'wp_login_failed', $username );
								return new WP_Error("{$this->prefix}login_error", __('<strong>Libsys and LDAP Login Error</strong>: LDAP credentials are correct and user creation is allowed but an error occurred creating the user in WordPress. Actual error: '.$new_user->get_error_message() ));
							}
		
						} else {
							return new WP_User($user->ID);
						}
					} else {
						return new WP_Error("{$this->prefix}login_error", __('<strong>Libsys and LDAP Login Error</strong>: Your LDAP credentials are correct, but you are not in an authorized LDAP group.'));
					}
				}


		} 

		do_action($this->prefix . 'auth_failure');
		return false;
	}

	function ldap_auth( $username, $password, $directory ) {
		$result = false;

		if ( $directory == "ad" ) {
			$result = $this->adldap->authenticate( $username, $password );
		} elseif ( $directory == "ol" ) {
			$this->ldap = ldap_connect( join(' ', (array)$this->get_setting('domain_controllers')), (int)$this->get_setting('ldap_port') );
			ldap_set_option($this->ldap, LDAP_OPT_PROTOCOL_VERSION, (int)$this->get_setting('ldap_version'));
			if ( str_true($this->get_setting('use_tls')) ) {
				ldap_start_tls($this->ldap);
			}
		$ldapbind = @ldap_bind($this->ldap, $this->get_setting('ol_login') .'=' . $username . ',' . $this->get_setting('base_dn'), $password); 

        $result = $ldapbind; 
		}

		return apply_filters($this->prefix . 'ldap_auth', $result);
	}
	function libsys_auth( $username, $password ) {
		$result = false;

		

		return apply_filters($this->prefix . 'libsys_auth', $result);
	}

	function user_has_groups( $username = false, $directory ) {
		$result = false;
		$groups = (array)$this->get_setting('groups');
		$groups = array_filter($groups);

		if ( ! $username ) return $result;
		if ( count( $groups ) == 0 ) return true;

		if ( $directory == "ad" ) {
			foreach ($groups as $gp) {
				if ( $this->adldap->user_ingroup ($username, $gp ) ) {
					$result = true;
					break;
				}
			}
		} elseif ( $directory == "ol" ) {
			if( $this->ldap === false ) return false;

			$result = ldap_search($this->ldap, $this->get_setting('base_dn'), '(' . $this->get_setting('ol_login') . '=' . $username . ')', array('cn'));
			$ldapgroups = ldap_get_entries($this->ldap, $result);

			// Ok, we should have the user, all the info, including which groups he is a member of.
			// Let's make sure he's in the right group before proceeding.
			$user_groups = array();
			for ( $i = 0; $i < $ldapgroups['count']; $i++) {
				$user_groups[] .= $ldapgroups[$i]['cn'][0];
			}
			//echo "DN:user_groups:";
        	//var_dump($user_groups); 
			$result =  (bool)(count( array_intersect($user_groups, $groups) ) > 0);
		}

		return apply_filters($this->prefix . 'user_has_groups', $result);
	}

	function get_user_data( $username, $directory ) {
		$user_data = array(
			'user_pass' => md5( microtime() ),
			'user_login' => $username,
			'user_nicename' => '',
			'user_email' => '',
			'display_name' => '',
			'first_name' => '',
			'last_name' => '',
			'role' => $this->get_setting('role')
		);

		if ( $directory == "ad" ) {
			$userinfo = $this->adldap->user_info($username, array("samaccountname","givenname","sn","cn","mail"));
			$userinfo = $userinfo[0];
		} elseif ( $directory == "ol" ) {
			if ( $this->ldap == null ) {return false;}

			$result = ldap_search($this->ldap, $this->get_setting('base_dn'), '(' . $this->get_setting('ol_login') . '=' . $username . ')', array($this->get_setting('ol_login'), 'sn','cn', 'givenname', 'uid','memberof'));
			$userinfo = ldap_get_entries($this->ldap, $result);

			if ($userinfo['count'] == 1) {
				$userinfo = $userinfo[0];
			}
		} else return false;

		if( is_array($userinfo) ) {
			$regex = '#^cn=([^,]*),#';
			$str= $userinfo['memberof'][0];
			$memberof = "";
			if(preg_match($regex, $str, $matches)){
				$memberof=$matches[1];
			}
			$user_data['user_nicename'] = $userinfo['givenname'][0] . ' ' . $userinfo['cn'][0]. '-' . $memberof;
			$user_data['user_email'] 	= $userinfo['uid'][0]. "@niit.edu.cn";
			$user_data['display_name']	= $user_data['user_nicename'];
			$user_data['first_name']	= $userinfo['givenname'][0];
			$user_data['last_name'] 	= $userinfo['cn'][0];

		}

		return apply_filters($this->prefix . 'user_data', $user_data);
	}
	function get_libsys_user_data( $username,$userinfo ) {
		$user_data = array(
			'user_pass' => md5( microtime() ),
			'user_login' => $username,
			'user_nicename' => '',
			'user_email' => '',
			'display_name' => '',
			'first_name' => '',
			'last_name' => '',
			'role' => $this->get_setting('role')
		);

		
		if( is_array($userinfo) ) {
			
			$user_data['user_nicename'] = $userinfo['name'] . ' ' . $userinfo['dept']. ' ' .  $userinfo['dep_name'];
			$user_data['user_email'] 	= "";
			$user_data['display_name']	= $user_data['user_nicename'];
			$user_data['first_name']	= $user_data['user_nicename'];
			$user_data['last_name'] 	= "";

		}

		return apply_filters($this->prefix . 'user_data', $user_data);
	}
}

if ( ! function_exists('str_true') ) {
	/**
	 * Evaluates natural language strings to boolean equivalent
	 *
	 * Used primarily for handling boolean text provided in shopp() tag options.
	 * All values defined as true will return true, anything else is false.
	 *
	 * Boolean values will be passed through.
	 *
	 * Replaces the 1.0-1.1 value_is_true()
	 *
	 * @author Jonathan Davis
	 * @since 1.2
	 *
	 * @param string $string The natural language value
	 * @param array $istrue A list strings that are true
	 * @return boolean The boolean value of the provided text
	 **/
	function str_true ( $string, $istrue = array('yes', 'y', 'true','1','on','open') ) {
		if (is_array($string)) return false;
		if (is_bool($string)) return $string;
		return in_array(strtolower($string),$istrue);
	}
}

$SimpleLDAPLibsysLogin = SimpleLDAPLibsysLogin::getInstance();
