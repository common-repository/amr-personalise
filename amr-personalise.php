<?php /*
Plugin Name: amr personalise
Plugin URI: http://webdesign.anmari.com/plugins/personalise/
Author: anmari
Author URI: http://anmari.com/
Version: 2.10
Text Domain: amr-personalise 
Description:  Allows inclusion of user values in content, and emails.  Insert [user] for name, or [user metakey] to access other metadata, or [user mesage="... %s...." metakey].  For example: [user user_email] or [user user_login] .  Can also be used to set the sender of wordpress emails.
 
*/

require_once('amr-personalise-uninstall.php');
include_once('amr-functions.php');

define('AMR_PERSONALISE_VERSION', '2.10');
define('AMR_PRODUCT', 'AMR_PERSONALISE');
define('AMR_PERSONALISE_URL','https://wpusersplugin.com/personalise/');
define('AMR_PERSONALISE_RSS','https://wpusersplugin.com/feed/');
define ('APERS_URL', plugin_dir_url(__FILE__));
define ('APERS_DIR', plugin_dir_path(__FILE__));
//define ('APERS_URL', WP_PLUGIN_URL.'/amr-personalise');
//define ('APERS_DIR', WP_PLUGIN_DIR.'/amr-personalise');
define( 'AP_BASENAME', plugin_basename( __FILE__ ) );
global $df, $tf, $tz;

if (!($df = get_option('date_format'))) $df = 'D, F j, Y';
if (!($tf = get_option('time_format'))) $tf = 'g:i a';
if (!($tz = get_option('timezone_string'))) $tz = 'UTC';

	/**
	Adds a link directly to the settings page from the plugin page
	*/
function ap_plugin_action($links, $file) {

	/* create link */
	if ( $file == AP_BASENAME ) {
		array_unshift(
			$links,
			sprintf( '<a href="options-general.php?page=%s">%s</a>', AP_BASENAME, __('Settings') )
		);
	}
	return $links;
} // end plugin_action()

function ap_admin_header() {

	echo '<ul class="subsubsub">';
	echo '<li><a target="_blank" href="http://wordpress.org/tags/amr-personalise?forum_id=10">';
	_e('Free Support','amr-personalise');
	echo '</a> | </li>';	
	echo '<li><a target="_blank" href="https://wpusersplugin.com/support">';
	_e('Support at plugin home','amr-personalise');
	echo '</a> | </li>
	<li><a target="_blank" href="https://wordpress.org/extend/plugins/amr-personalise/">';
	_e('Rate it at Wordpress','amr-personalise');
	echo '</a> | </li>
	<li>
	<a target="_blank" href="https://wpusersplugin.com/feed/">';
	_e('Subscribe for updates - Rss feed','amr-personalise');
	echo '</a></li></ul>';
	echo '<div class="clear"></div>';

	return;
}
		
function ap_help() {
	$log_file = @ini_get('error_log');
	$maybe_log_url = content_url('debug.log');
	$test_content = 'Dear [user user_nicename], your login username is [user user_login] and your email is [user user_email]. <br />'
	.'[user message="You registered on %s as a %s " user_registered wp_capabilities ]<br />'
	.'[user message="You entered a url %s" user_url empty="You have not entered a url"]';
	
	echo '<div style="width: 90%" class="postbox">'
	.'<div class="handlediv"> </div>'
	.'<h3 class="hndle"><span>'.__('Help').'</span></h3><div class="inside">'
	.'<p><ul>'
	.'<li>'
	.'<a href="'
	.get_admin_url('','post-new.php?post_type=page&content='.
	'Dear [user user_nicename], your login username is [user user_login] and your email is [user user_email]')
	.'">'.__('Create a personalised page','amr-personalise').'</a>'
	.'</li>'
	.'<li>'
	.__('Use shortcodes like [user metakey] in html in a page or post or a text widget.','amr-personalise' )
	.'</li>'
	.'<li>'
	.'eg: <br />'.$test_content
	.'</li>'
	.'<li>'
	.__('See also:','amr-personalise').'<a href="http://wordpress.org/extend/plugins/amr-personalise/installation/">http://wordpress.org/extend/plugins/amr-personalise/installation/</a>'
	.'</li>'
	.'<li>'
	.__('Or show a test user below','amr-personalise')
	.'</li>'
	
	.'<li><b>'.__('Admin users only','amr-personalise').'</b> '.__('Add "?ID=x" to the page url to dump all user info for user with id x.' ).'</li>'
	.'<li>'.__('If WP_DEBUG is on, a debug log may be available here: ','amr-personalise' )
	.$log_file
	.' '.__('or try here:','amr-personalise').'<a href="'.$maybe_log_url.'">'.$maybe_log_url.'</a>'
	.' '
	.'<a title="info on debug" target="_blank" href="http://codex.wordpress.org/Editing_wp-config.php#Configure_Error_Log">?</a>'
	.'</li>'
	.'</ul>
	</p>
	<p>'.
	__('You may find these user related plugins helpful too:','amr-personalise' )
	.'<ul>'
	.'<li><a target="_blank" href="http://wpusersplugin.com/user-lists/">amr users (users lists)</a><li>'
	.'<li><a target="_blank" href="http://wpusersplugin.com/amr-user-templates/">amr user templates (simplified admin screens for users)</a><li>'
	.'</ul>'
	.'</p>
	</div></div>';
}	

function amr_firstname ($user) {
/* expects a user object generated by get_currentuserinfo() or get_userdata()
 */
	if (!empty ($user->user_firstname)) return (ucwords($user->user_firstname));	
	else 
	if (!empty ($user->display_name)) return (ucwords($user->display_name));
	else 
	if (!empty ($user->user_nicename)) return (ucwords($user->user_nicename));
	else 
	if (!empty ($user->user_login)) return (ucwords($user->user_login));
	else 
	if (!empty ($user->user_email)) return (ucwords($user->user_email));
	else {
		$ifnoname = get_option('ap-ifnoname');
		if (!is_null($ifnoname)) return ($ifnoname);
		else return('');
	}
}

function amr_format_datetime ($dt_string) {
/* expects a string ala 2009-06-01 03:52:48
 */
global $df, $tf, $tz;
	return( date_i18n( $df.' '.$tf, strtotime($dt_string)));
}

function amr_personalise ($user, $metakey) {
/* look for metafield and echo */
	$nodata = '';
	if (!is_object ($user)) {
		if ($metakey==='display_name') {
			$ifnoname = get_option('ap-ifnoname');
			If (!is_null($ifnoname)) 
				return ($ifnoname);
		}
		else 
		return('No user object:'.print_r($user, true));		
	}

	if ($metakey === 'display_name') 
		return amr_firstname ($user);
	else if ($metakey === 'user_registered') {
		if (isset ($user->$metakey)) { 
			return amr_format_datetime ($user->$metakey);
		}
		else return ($nodata);
		}
	if (stristr($metakey,'capabilities')) {
		//$test = $user->$metakey;
		//echo ('Yse: '.$test);
		if (is_array ($user->$metakey)) {
			$text = '';
			foreach ($user->$metakey as $c => $tf) {
				if ($tf == '1') $text = $text.', '.$c; 
			}

			return (trim( $text,', '));
		}
		else return ($nodata);
	}	
	else { 
	if (!empty ($user->$metakey)) { 
		$value = $user->$metakey;
		if (is_array ($value)) 
			return (implode(', ', $value));
		else {
			return ($value);
			}
		}
	else { //echo 'trying for '.$metakey;
		return ($nodata);
		}
	}

}
	
/* insert the specific user parameters into the message at the place holders */
function a_personalised_message($user_info, $text, $atts) {
 
 /* EG: You registered on %s with user name %s */
	$c = substr_count($text, '%s');  
 
	foreach ($atts as $i => $v) { 
			if (!($atts[$i] = amr_personalise($user_info, $v)) or stristr($atts[$i],'<!--')) {
				$atts[$i] = 'n/a';
			}	
			else if ($atts[$i] === $v) 
				unset ($atts[$i]);			
	}
	
	
	if (empty($atts) or ($c > count($atts)) ) {
		return('<!-- Message "'.$text.'" ignored - inadequate data for this user. -->');	
	}
	
	else 
		return (vsprintf ($text, $atts));
 
 }

function ap_dump_user_and_meta ($user_id) {
		if (empty ($user_id)) {
			echo '<h2>No user id entered.</h2>';
			return;
		}
		$user_info = get_userdata($user_id);
		if (!is_object($user_info)) {
			echo '<h2>Fetch on user id '.$user_id.' failed - no user object found.</h2>';
			var_dump($user_id);
			return;
		}
		$data = $user_info->data;
		// security risk - do not dump user details to public log file// if (WP_DEBUG_LOG) error_log('DEBUG: userinfo by request'.print_r($user_info , true));
		echo '<h3>Dumping user data by request: </h3>';
		echo '<table>';
		foreach ($data as $i => $info) {
			
			if (!($i == 'user_pass')) {
				echo '<tr>';
				echo '<th>'. $i. '</th>'; 
				echo '<td>';
				print_r($info);
				echo '</td>';
				echo '</tr>';
			}
			
		};
		$metadata = ap_get_user_meta ($user_id);
		foreach ($metadata as $i => $meta) {
			if (!($i == 'user_pass')) {
				echo '<tr>';
				echo '<th>'.$i.'</th> '; 
				echo '<td>';
				print_r($meta);
				echo '</td>';
				echo '</tr>';
			}
		};
		echo '</table>';
		return $data;
}

function ap_check_excluded_user_meta ($data) {
	$exclude = array(
		'user_pass',
		'dismissed_wp_pointers',
		'user-settings',
		'user-settings-time'
	);
	foreach ($data as $mk => $mv) {
		foreach ($exclude as $text) { 
			
			if (stristr ($mk,$text)) { 
				unset ($data[$mk]);
			}
		}
	}
	return $data;
}

function ap_get_user_meta ($user_id) {
global $wpdb;

	$sql =  "SELECT meta_key, meta_value FROM $wpdb->usermeta WHERE user_id=$user_id  ";  
	$results = $wpdb->get_results($sql, ARRAY_A);
	foreach ($results as $i => $mkv) {
		//echo '<br /><em>'.$mkv['meta_key'].'</em> '.$mkv['meta_value'];
		
		$data[$mkv['meta_key']] = maybe_unserialize($mkv['meta_value']);
	}
	$data = ap_check_excluded_user_meta ($data);
	ksort($data);
	return ($data);
}

function ap_user_shortcode ($atts, $otherargs='') {
/* If no atts, then just display name if have else user default */
/* shortcode can  be any of the values from the user or user meta tables 
user_login,  user_nicename, user_email, user_url, display_name

Shortcode usage:
 [user] or [user display_name]- will give display name or default from settings
 [user user_email] will give email address if it exists
 
 */
global $current_user;

	$result = '';

	if (current_user_can('edit_users') and (isset($_REQUEST['ID'])) and (is_numeric ($_REQUEST['ID']))) {
		$user_info = ap_dump_user_and_meta ($_REQUEST['ID']);
		
	}
	else {
		if (!empty($atts['to'])) {
			$user_info = get_user_by_email($atts['to']);
		}
		else {
			$user_info = wp_get_current_user();
			//get_currentuserinfo();
			//$user_info = $current_user; //var_dump($user_info);
		}
		
	}
	/* allow for potentially a request for more than one value */
	if (!is_array($atts)) {
		return( amr_firstname ($user_info));
		}
	else {
		if (!empty($atts['empty'])) {
			$empty = $atts['empty']; 
			unset ($atts['empty']);		
		}
		else 
			$empty = '';
			
			
		if (!empty($atts['message'])) {
			$text = $atts['message']; 
			unset ($atts['message']);
			if (function_exists('a_personalised_message')) 
				$result = a_personalised_message($user_info, $text, $atts);
		}
		else {
			foreach ($atts as $i => $v) {  
				if (!empty($result)) 
					$result .= ' ';
					
				$thistext = amr_personalise ($user_info, $v);	
				
				$result .= $thistext;
				//echo '<br />why'; var_dump($result);
			}
			
		}	
		if (empty($result)) {
			$result = $empty;
		}
		else {
			//echo '<br />Result not empty';
			//var_dump($result);
		}
			
	}
	
	return ($result);
}	

function ap_admin_menu() { /* parent, page title, menu title, access level, file, function */
	$plugin_page = add_submenu_page('options-general.php', 
		__('Personalise','amr-personalise'),
		__('Personalise','amr-personalise'),
		'manage_options',
		__FILE__,
		'ap_options_panel');

}

function ap_mail_from (&$atts) {
		$name = get_option('ap-fromaddr');
		if (isset($name)) return ($name);
		else return ($atts);
}

function ap_mail_from_name (&$atts) {
		$name = get_option('ap-fromname');
		if (isset($name)) return ($name);
		else return ($atts);
}

function ap_user_mailfilter ($atts) {
/* apply the shortcode functionality to outgoing email messages */

	if (isset ($atts ['to'])) {
		/*if ( $user = get_user_by('email',$atts['to'] )) {
			$name = amr_firstname ($user);
		}
		else $name = get_option('ap-ifnoname');
		*/
		if (isset($atts['message'])) 
			$atts['message'] = do_shortcode($atts['message']);
		if (isset ($atts['subject'])) 
			$atts ['subject'] = do_shortcode($atts['subject']);

	}
	if (WP_DEBUG) {
		error_log('Mail being sent to '.print_r($atts['to'], true));
		error_log('Mail header '.print_r($atts['headers'], true));
//		error_log('Mail being sent bcc '.print_r($atts['bcc'], true));
	}	
	return ($atts);
}

	if (is_admin() )	{
		load_plugin_textdomain('amr-personalise', false , basename(dirname(__FILE__)) );
		add_action('admin_menu', 'ap_admin_menu');	

		add_filter('plugin_action_links', 'ap_plugin_action', -10, 2);
		
	}
	else add_filter('widget_text', 'do_shortcode', 11 /*SHORTCODE_PRIORITY*/);  /* Need priority to get the filter to load early */

	/* does apply filters apply the shortcodes too */
	add_shortcode('user', 'ap_user_shortcode');
	add_filter('wp_mail','ap_user_mailfilter'); 
	If ($enable = get_option('ap-enable-senderchange')) {
		add_filter('wp_mail_from','ap_mail_from'); 
		add_filter('wp_mail_from_name','ap_mail_from_name');
	}

if ( ! function_exists('wpmq_mail') ) {
	function wpmq_mail() { // a dummy function so that S2 sendingto single recipient will work , because it cheks this fn and limit = 1, seeline 363
	};
}
		
	function ap_options_panel() {
//	if (!check_admin_referer('amr-personalise')) die;

	if (isset($_REQUEST['uninstall'])  OR isset($_REQUEST['reallyuninstall']))  { /*  */
		amr_personalise_uninstall(); 	
		return;
	}
	elseif (!empty($_POST['Dump']) and !empty($_POST['id'])) { 
				$id = (int) $_POST['id'];
				ap_dump_user_and_meta ($id);
				return;
	}
	else {
		$ifnoname = get_option('ap-ifnoname');	
		if (!($ifnoname)) $ifnoname = '';
		$fromaddr = get_option('ap-fromaddr');
		$fromname = get_option('ap-fromname');
		$enable = get_option('ap-enable-senderchange');
		if (empty($enable)) $enable=false;
		
		if ((isset($_POST['action'])) and ($_POST['action'] == "save")) {/* Validate the input and save */

			if (isset($_POST['ifnoname'])) {
				$ifnoname = $_POST['ifnoname'];
				update_option('ap-ifnoname', $ifnoname);	
				}
			if (isset($_POST['fromaddr'])) {
				$fromaddr = $_POST['fromaddr'];
				update_option('ap-fromaddr', $fromaddr);	
				}
			if (isset($_POST['fromname'])) {
				$fromname = $_POST['fromname'];
				update_option('ap-fromname', $fromname);	
				}	
			if (isset($_POST['enable'])) {
				if ($_POST['enable'] === 'true') $enable = true;
				else $enable = false;
				update_option('ap-enable-senderchange', $enable);	
				}
			else {
				$enable = false; 
				update_option('ap-enable-senderchange', false);	}	
		}?>	
		<div class="wrap" id="amr-personalise">
		<div id="icon-users" class="icon32">
		<br/>
		</div>
		<h2><?php echo __('amr personalise ', 'amr-personalise').' '.AMR_PERSONALISE_VERSION.'</h2>'.PHP_EOL;
		?>
		</div><!-- icon-users -->
		<div class="metabox-holder">		
			<form method="post" action="<?php htmlentities($_SERVER['PHP_SELF']); ?>">
				<?php  wp_nonce_field('amr-personalise'); /* outputs hidden field */?>
				
				<div>
				<?php ap_admin_header(); 
				ap_help();
				?>
				<h3><?php _e('User or Recipient modifications','amr-personalise'); ?></h3>
				<ul><li>
					<label for="ifnoname"><?php _e('Default text to display if no user name:', 'amr-personalise'); ?>
					</label>
					<input type="text" size="20" id="ifnoname" name="ifnoname" value="<?php echo $ifnoname;  ?>" />
				</li></ul>
				</div>	
				<h2><?php _e('Settings if using "personalise" in email templates:','amr-personalise');?></h2>
				<div><h3><?php _e('Email sender modifications','amr-personalise'); ?></h3>
				<p><?php _e('Uses the wp_mail filter.' ,'amr-personalise');  
				echo '<br />';
				_e('Other plugins may also be modifying the filter.','amr-personalise'); 
				echo '<br />';
				_e('Typically the last to filter will affect the final result.','amr-personalise'); 
				echo '<br />';
				_e('This can depend on priorities or activation sequence if priorities are the same.','amr-personalise'); 
				echo '<br />';
				_e('Some other plugins overwrite the wp_mail function entirely and may or may not apply the filter.','amr-personalise'); 
				?>
				</p>
				<ul><li>
					<label for="enable"><?php _e('Enable Sender Email Customisation','amr-personalise'); ?>
					</label>
					<input type="checkbox" size="20" id="enable" name="enable" value="true" <?php if ($enable) echo 'checked="checked" '; ?> />
					</li><li>
					<label for="fromaddr"><?php _e('Default email "from address"','amr-personalise'); ?>
					</label>
					<input type="text" size="20" id="fromaddr" name="fromaddr" value="<?php echo $fromaddr;  ?>" />
					</li><li>
					<label for="fromname"><?php _e('Default email "from name"','amr-personalise'); ?>
					</label>
					<input type="text" size="20" id="fromname" name="fromname" value="<?php echo $fromname;  ?>" />
					</li></ul>
				</div>
				<div id="submit">
					<input type="hidden" name="action" value="save" />
					<input type="submit" class="button-primary" value="<?php _e('Update', 'amr-personalise') ?>" />
					<input type="submit" class="button" name="uninstall" value="<?php _e('Uninstall', 'amr-personalise') ?>" />		
				</div>
				<div>
				<h3><?php _e('Show possible user meta keys for an ID','amr-personalise'); ?></h3>
					<label for="ID"><?php _e('Test ID:','amr-personalise'); ?>
					</label>
					<input type="text" size="5" id="ID" name="id" value=" " />
					<input type="submit" class="button" name="Dump" value="<?php _e('Show', 'amr-personalise') ?>" />
				</div>
			</form>
		</div>
<?php		
		}
	}	//end option_page	
