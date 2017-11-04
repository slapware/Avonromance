<?php
/*
Plugin Name: SLAP Avon Author addition plugin
Plugin URI: http://developer.harpercollins.com
Description: Adds Avon Authors by global ID.
Version: 2.0.1
Author: Stephen La Pierre
Author URI: http://shibashake.com
*/


/*  Copyright 2012  ShibaShake

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
// don't load directly
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

define( 'AVON_AUTHOR_API_VERSION', '2.0.2' );
define( 'AVON_AUTHOR_API_RELEASE_DATE', date_i18n( 'F j, Y', '1397937230' ) );
define( 'AVON_AUTHOR_API_DIR', plugin_dir_path( __FILE__ ) );
define( 'AVON_AUTHOR_API_URL', plugin_dir_url( __FILE__ ) );


if (!class_exists("Harper_AVON_AUTHOR_API")) :

class Harper_AVON_AUTHOR_API {
	var $settings, $options_page;

	function __construct() {

		if (is_admin()) {
			// Load example settings page
			if (!class_exists("Harper_AVON_AUTHOR_API_Settings"))
				require(AVON_AUTHOR_API_DIR . 'harper-avon-author-settings.php');
			$this->settings = new Harper_AVON_AUTHOR_API_Settings();
		}

		add_action('init', array($this,'init') );
		add_action('admin_init', array($this,'admin_init') );
		add_action('admin_menu', array($this,'admin_menu') );

		register_activation_hook( __FILE__, array($this,'activate') );
		register_deactivation_hook( __FILE__, array($this,'deactivate') );
	}

	/*
		Propagates pfunction to all blogs within our multisite setup.
		More details -
		http://shibashake.com/wordpress-theme/write-a-plugin-for-wordpress-multi-site

		If not multisite, then we just run pfunction for our single blog.
	*/
	function network_propagate($pfunction, $networkwide) {
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function
			// for each blog id
			if ($networkwide) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					call_user_func($pfunction, $networkwide);
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		call_user_func($pfunction, $networkwide);
	}

	function activate($networkwide) {
		$this->network_propagate(array($this, '_activate'), $networkwide);
	}

	function deactivate($networkwide) {
		$this->network_propagate(array($this, '_deactivate'), $networkwide);
	}

	/*
		Enter our plugin activation code here.
	*/
	function _activate() {}

	/*
		Enter our plugin deactivation code here.
	*/
	function _deactivate() {}


	/*
		Load language translation files (if any) for our plugin.
	*/
	function init() {
		load_plugin_textdomain( 'avon_authors', AVON_AUTHOR_API_DIR . 'lang',
							   basename( dirname( __FILE__ ) ) . '/lang' );
	}

	function admin_init() {
	}

	function admin_menu() {
	}

	function hc_avon_author_update() {
	    //		$GLOBALS['DebugMyPlugin']->panels['main']->addMessage('Got the add_action:','Hello SLAP');
	    $this->settings->hc_avon_author_update();
	}

    function author_update_missing_GID() {
        $this->settings->author_update_missing_GID();
    }
	/*
		Example print function for debugging.
	*/
	function print_example($str, $print_info=TRUE) {
		if (!$print_info) return;
		__($str . "<br/><br/>\n", 'avon_authors' );
	}

	/*
		Redirect to a different page using javascript. More details-
		http://shibashake.com/wordpress-theme/wordpress-page-redirect
	*/
	function javascript_redirect($location) {
		// redirect after header here can't use wp_redirect($location);
		?>
		  <script type="text/javascript">
		  <!--
		  window.location= <?php echo "'" . $location . "'"; ?>;
		  //-->
		  </script>
		<?php
		exit;
	}

} // end class
endif;

// Initialize our plugin object.
global $avon_authors;
if (class_exists("Harper_AVON_AUTHOR_API") && !$avon_authors) {
    $avon_authors = new Harper_AVON_AUTHOR_API();
	add_action('hc_doAuthorUpdate', array($avon_authors, 'hc_avon_author_update' ) );
	add_action('hc_doAuthMissingGID', array($avon_authors, 'author_update_missing_GID' ) );
}
?>