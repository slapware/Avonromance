<?php
/*
Plugin Name: Harper Avon Update Genre dashboard widget.
Plugin URI: http://developer.harpercollins.com
Description: Adds the dashboard panel to execute the book additions from the Harper Collins API content Plugin. Settings in Harper-Options need to be set for this panel to run.
Version: 1.4
Author: ETG HarperCollins
Author URI: http://developer.harpercollins.com.com
*/

// if ( ! current_user_can('update_plugins') )
// 	wp_die(__('You are not allowed to update plugins on this blog.'));

//Function to start the additions.
function avon_genre_update_widget() {
?>
<p>Pressing Genres will update all book Genres and will take a while to complete..
</p>
<form method="post" action="">
<input type="submit" value="Genres" name='genre'/>

</form>
<?php
    if(isset($_POST['genre']))
    {
    	do_action('hc_doGenreUpdate');
    }
};

//Function to add API feed to the dashboard.
function avon_genre_add_update_widget() {
	wp_add_dashboard_widget('avon_genre_add_update_widget', 'Harper Avon Genre Update', 'avon_genre_update_widget');
}

//Action that calls the function that adds the widget to the dashboard.
    if (function_exists('is_multisite') && is_multisite()) {
		add_action('wp_user_dashboard_setup', 'avon_genre_add_update_widget');
		}
		else{
		add_action('wp_dashboard_setup', 'avon_genre_add_update_widget');
		}
?>