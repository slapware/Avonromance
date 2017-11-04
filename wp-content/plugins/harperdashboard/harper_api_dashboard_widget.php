<?php
/*
Plugin Name: Harper API dashboard add Avon Authors panel.
Plugin URI: http://developer.harpercollins.com
Description: Adds the dashboard panel to execute the book additions from the Harper Collins API content Plugin. Settings in Harper-Options need to be set for this panel to run.
Version: 1.3
Author: ETG HarperCollins
Author URI: http://developer.harpercollins.com.com
*/

// if ( ! current_user_can('update_plugins') )
// 	wp_die(__('You are not allowed to update plugins on this blog.'));

//Function to start the additions.
function harper_api_additions_function() {
?>
<p>Pressing submit will use the settings configured under Harper Options to add new authors to the current wordpress site.
If any errors on values in the setting are present, or no records are found for setting configured then the values used will be displayed for review and correction.</p>
<p>Once submit is pressed please wait for this panel to re-appear along with the count of additions made (or message) to be displayed to complete the additions. Depending on how far
back the date is set it may take a few minutes to add all the content, the further back the date the more data and time to process.
</p>
<form method="post" action="">
<input type="submit" value="submit" name='submit'/>

					</form>
<?php
    if(isset($_POST['submit']))
{
	do_action('hc_avonAuthUpdate');
}
};

//Function to add API feed to the dashboard.
function harper_api_add_dashboard_widget() {
	wp_add_dashboard_widget('harper_api_add_widget', 'Harper Content fetch new content', 'harper_api_additions_function');
}

//Action that calls the function that adds the widget to the dashboard.
    if (function_exists('is_multisite') && is_multisite()) {
		add_action('wp_user_dashboard_setup', 'harper_api_add_dashboard_widget');
		}
		else{
		add_action('wp_dashboard_setup', 'harper_api_add_dashboard_widget');
		}
?>