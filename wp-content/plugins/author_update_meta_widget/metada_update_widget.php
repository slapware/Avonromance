<?php
/*
Plugin Name: Harper API dashboard update missing GID.
Plugin URI: http://developer.harpercollins.com
Description: Adds the dashboard panel to execute the update postmeta from the Harper Collins API content Plugin.
Version: 1.1
Author: ETG HarperCollins
Author URI: http://developer.harpercollins.com.com
*/

// if ( ! current_user_can('update_plugins') )
// 	wp_die(__('You are not allowed to update plugins on this blog.'));

//Function to start the additions.
function auth_meta_update_widget() {
?>
<p>Pressing submit will update wp_postmeta for authors with missing Global ID it may take a few
minutes to add all the content.
</p>
<form method="post" action="">
<input type="submit" value="submit" name='authmeta'/>

					</form>
<?php
    if(isset($_POST['authmeta']))
{
	do_action('hc_doAuthMissingGID');
}
};

//Function to add API feed to the dashboard.
function author_update_meta_widget() {
	wp_add_dashboard_widget('author_update_meta_widget', 'Author meta update', 'auth_meta_update_widget');
}

//Action that calls the function that adds the widget to the dashboard.
    if (function_exists('is_multisite') && is_multisite()) {
		add_action('wp_user_dashboard_setup', 'author_update_meta_widget');
		}
		else{
		add_action('wp_dashboard_setup', 'author_update_meta_widget');
		}
?>