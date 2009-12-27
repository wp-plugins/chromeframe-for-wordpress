<?php

/* 
Plugin Name: ChromeFrame-For-Wordpress
Plugin URI: http://sardiusgroup.com/chromeframe-for-wordpress
Version: 0.1
Author: Andrew Janssen, for The Sardius Group LLC
Description: Displays a ChromeFrame notice if the user is running IE6 or 7. Prompts the user to either upgrade to IE8, install ChromeFrame, or install another browser.
*/

function cf_header_content() {
	echo '<script src="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/js/jquery-1.3.2.min.js" type="text/javascript"></script>';
	echo '<script src="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/js/boxy-0.1.4/src/javascripts/jquery.boxy.js" type="text/javascript"></script>';

	echo '<!--[if IE 7]>';
	echo '	<link rel="stylesheet" href="' .
		get_bloginfo('wpurl') . '/wp-content/plugins/chromeframe-for-wordpress/js/boxy-0.1.4/src/stylesheets/boxy.ie7.css" />';
	echo '<![endif]-->';
	
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"> </script>
<script>
  $(document).ready(function() {
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
		if (version < 8) {
			new Boxy("<p>Sorry, your browser (Internet Explorer " + 
				version + ") is out of date and doesn't support<br/>modern Web pages like sardiusgroup.com. " +
				"Please <a href=\"http://www.microsoft.com/windows/internet-explorer/worldwide-sites.aspx\">upgrade</a> " +
				"your browser to<br/>version 8 or install <a href=\"http://code.google.com/chrome/chromeframe/\">this browser enhancement</a> before continuing." +
				" Thank you!</p><p><a href=\"http://www.mozilla.com/en-US/firefox/firefox.html\">Mozilla FireFox</a>, <a href=\"http://www.google.com/chrome\">Google Chrome</a>, <a href=\"http://www.opera.com/browser/\">Opera</a>," +
				" and <a href=\"http://www.apple.com/safari/download/\">Apple Safari</a> are great options too.</p>", { title: "Browser Upgrade Required (Free)",
				closeable: false,
				modal: true
			} );
		}
	};
 CFInstall.check({
    onmissing: onIE
  });
});
</script>

<?php
}

add_action('wp_head', 'cf_header_content' );

?>