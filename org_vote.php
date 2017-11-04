<?php
require_once('wp-load.php');
global $wpdb;
$table_name = $wpdb->prefix . "votes";
$ip = $_SERVER['REMOTE_ADDR']; 
$post_id = mysql_escape_string($_GET['q']);

$randomFact = $wpdb->get_row("SELECT * FROM wp_votes WHERE post_id='$post_id' AND ip_address='$ip'" , ARRAY_N);

$NumRows = count((array) $randomFact);
if($NumRows<=0)
{
	$wpdb->insert( $table_name, array( 'post_id' => $post_id, 'ip_address' => $ip ) );
	$jsonarray = array('ok'=>true);
	echo json_encode($jsonarray);
}
else
{
	$jsonarray = array('ok'=>false);
	echo json_encode($jsonarray);
}

?>