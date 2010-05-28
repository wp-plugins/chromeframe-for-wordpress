<?php

/* 
Plugin Name: ChromeFrame-For-Wordpress
Plugin URI: http://sardiusgroup.com/chromeframe-for-wordpress
Version: 0.2.005
Author: Andrew Janssen, for The Sardius Group LLC
Description: Displays a notice if the user is running IE6 or 7. Prompts the user to either upgrade to IE8, install ChromeFrame, or install another browser. Adds ChromeFrame support to the page.
*/

$plugin_title = 'ChromeFrame-For-Wordpress';
$plugin_id = 'chromeframe-for-wordpress';

$options_page_title = 'ChromeFrame-For-Wordpress Options';

$html_option_name = 'chromeframe-for-wordpress-message-html';
$enabled_option_name = 'chromeframe-for-wordpress-enabled';
$versions_option_name = 'chromeframe-for-wordpress-ie-versions';

function cf_install() {
	$default_message_html = "<p>Sorry, your browser is out of date and doesn't support modern Web pages like " . bloginfo('blogname') . '. ' .
	"Please <a href=\"http://www.microsoft.com/windows/internet-explorer/worldwide-sites.aspx\">upgrade</a> " .
	"your browser to version 8 or install <a href=\"http://code.google.com/chrome/chromeframe/\">this browser enhancement</a> before continuing." .
	" Thank you!</p><p><a href=\"http://www.mozilla.com/en-US/firefox/firefox.html\">Mozilla FireFox</a>, <a href=\"http://www.google.com/chrome\">Google Chrome</a>, <a href=\"http://www.opera.com/browser/\">Opera</a>," .
	" and <a href=\"http://www.apple.com/safari/download/\">Apple Safari</a> are great options too.</p>";

	add_option($html_option_name, $default_message_html);
	add_option($versions_option_name, array('5', '6', '7'));	
}

function cf_uninstall() {
	delete_option($html_option_name);
	delete_option($enabled_option_name);
	delete_option($versions_option_name);
}

function get_ie_version() {
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (preg_match('/\bmsie 6/i', $ua) && !preg_match('/\bopera/i', $ua)) {
	 	return 6;
	} elseif (preg_match('/\bmsie 7/i', $ua) && !preg_match('/\bopera/i', $ua)) {
		return 7;
	} elseif (preg_match('/\bmsie 8/i', $ua) && !preg_match('/\bopera/i', $ua)) {
		return 8;
	}
	
	return -1;
}

function cf_header_content() {
	global $versions_option_name, $html_option_name;	
		
	echo '<script type="text/javascript" src="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/js/jquery-1.3.2.min.js" type="text/javascript"></script>';
	echo '<script type="text/javascript">jQuery.noConflict();</script>';

	echo '<link rel="stylesheet" href="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/js/boxy-0.1.4/src/stylesheets/boxy.css" />';

	echo '<script src="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/js/boxy-0.1.4/src/javascripts/jquery.boxy.js" type="text/javascript"></script>';
	echo '	<link rel="stylesheet" href="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/css/boxy_overrides.css" />';
	echo '<![endif]-->';
			
	echo '<!--[if IE 6]>';
	echo '	<link rel="stylesheet" href="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/css/boxy.ie6.css" />';
	echo '<![endif]-->';

	echo '<!--[if IE 7]>';
	echo '	<link rel="stylesheet" href="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/css/boxy.ie7.css" />';
	echo '<![endif]-->';
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"> </script>
<script>
  jQuery(document).ready(function() {
	// From http://msdn.microsoft.com/en-us/library/ms537509(VS.85).aspx
	// Returns the version of Internet Explorer or a -1
	// (indicating the use of another browser).
	function getInternetExplorerVersion()
	{
	  var rv = -1; // Return value assumes failure.
	  if (navigator.appName == 'Microsoft Internet Explorer')
	  {
	    var ua = navigator.userAgent;
	    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
	    if (re.exec(ua) != null)
	      rv = parseFloat( RegExp.$1 );
	  }
	  return rv;
	}
		
	var onIE = function() {
		var version = getInternetExplorerVersion();
		var blockedVersions = [<?php
			$is_first = true;
			foreach(get_option('chromeframe-for-wordpress-ie-versions') as $version) {
				if ($is_first == false) {
					echo ',';
				}
				echo $version;
				$is_first = false;
			}
?>];
		var ieAllowed = true;
		for(i=0;i<blockedVersions.length;i++){
	      if(blockedVersions[i] == version){
	        ieAllowed = false;
	      }
	    }
		if (!ieAllowed) {
			new Boxy(<?php
echo '"' . str_replace('"', '\"', get_option($html_option_name)) . '"';
?>, { title: "Browser Upgrade Required (Free)",
				closeable: false,
				modal: true
			} );
		}
	};
	if (getInternetExplorerVersion() != -1) {
		onIE();
	}
});
</script>
<?php

	$ie_version = get_ie_version();
	$versions = get_option($versions_option_name);
	if (in_array("$ie_version", $versions)) {
		echo '</head><body><noscript><table cellspacing="0" cellpadding="0" border="0" class="boxy-wrapper fixed" style="z-index: 1339; visibility: visible; left: 441.5px; top: 275.5px; opacity: 1;"><tbody><tr><td class="top-left"></td><td class="top"></td><td class="top-right"></td></tr><tr><td class="left"></td><td class="boxy-inner"><div class="title-bar"><h2>Browser Upgrade Required (Free)</h2></div><p>' . get_option($html_option_name) . '</p></td><td class="right"></td></tr><tr><td class="bottom-left"></td><td class="bottom"></td><td class="bottom-right"></td></tr></tbody></table><div class="boxy-modal-blackout" style="z-index: 1001; opacity: 0.7; width: 100%; height:100%;"></div></noscript></body>';
	}
}

function cf_form_table($rows) {
	$content = '<table class="form-table" width="100%">';
	foreach ($rows as $row) {
		$content .= '<tr><th valign="top" scope="row" style="width:50%">';
		if (isset($row['id']) && $row['id'] != '')
			$content .= '<label for="'.$row['id'].'" style="font-weight:bold;">'.$row['label'].':</label>';
		else
			$content .= $row['label'];
		if (isset($row['desc']) && $row['desc'] != '')
			$content .= '<br/><small>'.$row['desc'].'</small>';
		$content .= '</th><td valign="top">';
		$content .= $row['content'];
		$content .= '</td></tr>'; 
	}
	$content .= '</table>';
	return $content;
}

function cf_postbox($id, $title, $content) {
?>
		<div id="<?php echo $id; ?>" class="postbox">
			<div class="handlediv" title="Click to toggle"><br /></div>
			<h3 class="hndle"><span><?php echo $title; ?></span></h3>
			<div class="inside">
				<?php echo $content; ?>
			</div>
		</div>
<?php
}

function cf_options_page() {
	global $versions_option_name, $html_option_name, $plugin_id;
?>
	<div class="wrap">
	<h2>ChromeFrame-For-WordPress</h2>
	<div class="postbox-container" style="width:70%;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">
							<form action="options.php" method="post">
								<?php wp_nonce_field('update-options'); ?>
								
								<?php 
								$enabled = $versions = $message_html = '';
								$enabled = get_option($enabled_option_name);
								$versions = get_option($versions_option_name);
								$ie5_checked = in_array('5', $versions) ? 'checked' : '';
								$ie6_checked = in_array('6', $versions) ? 'checked' : '';
								$ie7_checked = in_array('7', $versions) ? 'checked' : '';
								$ie8_checked = in_array('8', $versions) ? 'checked' : '';
								$message_html = get_option($html_option_name);
								$enabled_input_value = $enabled ? 'checked' : '';
								
								$rows[] = array(
										'id' => 'chromeframe-for-wordpress-message-html',
										'label' => 'HTML Message For Internet Explorer Users',
										'content' => "<textarea rows='6' columns='40' name='chromeframe-for-wordpress-message-html' id='chromeframe-for-wordpress-message-html'>$message_html</textarea>",
										'desc' => 'This message will be shown to users running selected versions of IE. <em>If you disable IE8, you\'ll want to change this message.</em>'
									);
								
								$versions_option_name_with_arr = $versions_option_name . '[]';
								$rows[] = array(
										'id' => $versions_option_name,
										'label' => 'Block These IE Versions',
										'desc' => 'Pick which versions of Internet Explorer to block.',
										'content' => "
										<ul>
										<li><label for='$plugin_id-ie8'><input type='checkbox' name='$versions_option_name_with_arr' id='$versions_option_name-ie8' value='8' $ie8_checked /> IE 8</label></li>
										<li><label for='$plugin_id-ie7'><input type='checkbox' name='$versions_option_name_with_arr' id='$versions_option_name-ie7' value='7' $ie7_checked /> IE 7</label></li>
										<li><label for='$plugin_id-ie6'><input type='checkbox' name='$versions_option_name_with_arr' id='$versions_option_name-ie6' value='6' $ie6_checked /> IE 6</label></li>
										<li><label for='$plugin_id-ie5'><input type='checkbox' name='$versions_option_name_with_arr' id='$versions_option_name-ie5' value='5' $ie5_checked /> IE 5</label></li>
										</ul>
										"
									);
								?>
								
								<?php cf_postbox("$plugin_id-settings", 'Settings', cf_form_table($rows));
								?>
								
								<input type="hidden" name="action" value="update" />
								<input type="hidden" name="page_options" value="<?php echo "$enabled_option_name,$html_option_name,$versions_option_name" ?>" />
								<input type="hidden" name="action" value="update" />
								<p class="submit">
								<input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes') ?>" />
								</p>
							</form>
						</div>
					</div>
				</div>
</div>
<?php	
}

function cf_plugin_menu() {
	global $options_page_title, $plugin_title, $plugin_id;
	
    add_options_page($options_page_title, $plugin_title, 'administrator', $plugin_id, 'cf_options_page');  
}

function cf_add_action_link( $links, $file ) {
	static $this_plugin;
	global $plugin_id;
	
	if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
	if ( $file == $this_plugin ) {
		$settings_link = '<a href="' . admin_url( "options-general.php?page=$plugin_id" ) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}

add_action('wp_head', 'cf_header_content');
add_action('admin_menu', 'cf_plugin_menu');
add_filter('plugin_action_links', 'cf_add_action_link', 10, 2 );
# TODO: in wp_content?, add a noscript block which floats above all other content.
register_uninstall_hook(__FILE__, 'cf_install');
register_uninstall_hook(__FILE__, 'cf_uninstall');
?>