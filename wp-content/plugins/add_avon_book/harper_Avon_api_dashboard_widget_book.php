<?php
/*
Plugin Name: Harper Avon Add Book dashboard widget.
Plugin URI: http://developer.harpercollins.com
Description: Adds the dashboard panel to execute the book additions from the Harper Collins API content Plugin. Settings in Harper-Options need to be set for this panel to run.
Version: 1.4
Author: ETG HarperCollins
Author URI: http://developer.harpercollins.com.com
*/

// if ( ! current_user_can('update_plugins') )
// 	wp_die(__('You are not allowed to update plugins on this blog.'));

//Function to start the additions.
function avon_book_addittions_widget() {
?>
<p>Pressing submit will use the settings configured under Harper Options to add new Book content to the Avon wordpress site.
If any errors on values in the setting are present, or no records are found for setting configured then the values used will be displayed for review and correction.</p>
<p>Once submit is pressed please wait for this panel to re-appear along with the count of additions made (or message) to be displayed to complete the additions.
 it will take a little while to complete..
</p>
<form method="post" action="">
<input type="submit" value="submit" name='submit'/>

</form>
<?php
    if(isset($_POST['submit']))
    {
    	do_action('hc_doAvonUpdate');
    }
};

//Function to add API feed to the dashboard.
function avon_book_add_dashboard_widget() {
	wp_add_dashboard_widget('avon_book_add_dashboard_widget', 'Harper Avon Books new content', 'avon_book_addittions_widget');
}

//Action that calls the function that adds the widget to the dashboard.
    if (function_exists('is_multisite') && is_multisite()) {
		add_action('wp_user_dashboard_setup', 'avon_book_add_dashboard_widget');
		}
		else{
		add_action('wp_dashboard_setup', 'avon_book_add_dashboard_widget');
		}
?>