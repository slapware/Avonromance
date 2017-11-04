<?php
/*
Plugin Name: Harper Avon CSV import dashboard widget.
Plugin URI: http://developer.harpercollins.com
Description: Adds the dashboard panel to execute the CSV import from the Harper Collins API content Plugin. The file/tmp/avon.csv is used.
Version: 1.2
Author: ETG HarperCollins
Author URI: http://developer.harpercollins.com.com
*/

// if ( ! current_user_can('update_plugins') )
// 	wp_die(__('You are not allowed to update plugins on this blog.'));

//Function to start the additions.
function avon_csv_loader_widget() {
?>
<p>Importing CSV will update all book Titles and will take a while to complete..
</p>
<form method="post" action="">
<input type="submit" value="CSVloader" name='csvloader'/>

</form>
<?php
    if(isset($_POST['csvloader']))
    {
    	do_action('hc_doCsvUpdate');
    }
};

//Function to add API feed to the dashboard.
function avon_add_csv_loader_widget() {
	wp_add_dashboard_widget('avon_add_csv_loader_widget', 'Harper CSV Loader Update', 'avon_csv_loader_widget');
}

//Action that calls the function that adds the widget to the dashboard.
    if (function_exists('is_multisite') && is_multisite()) {
		add_action('wp_user_dashboard_setup', 'avon_add_csv_loader_widget');
		}
		else{
		add_action('wp_dashboard_setup', 'avon_add_csv_loader_widget');
		}
?>