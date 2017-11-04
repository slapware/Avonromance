<?php
// added for addition of API author data
add_action('init', 'cptui_register_my_cpt_book_author' );

// added for addition of API author data
	// Modified Stephen La Pierre 11/4/14. At 8:55 PM
	function cptui_register_my_cpt_book_author() {
	register_post_type('book-author', array(
	'label' => 'Authors',
	'description' => '',
	'public' => true,
	'show_ui' => true,
	'show_in_menu' => true,
	'capability_type' => 'post',
	'map_meta_cap' => true,
	'hierarchical' => false,
	'rewrite' => array('slug' => 'book-author', 'with_front' => true),
	'query_var' => true,
	'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
	'labels' => array (
	  'name' => 'Authors',
	  'singular_name' => 'Author',
	  'menu_name' => 'Authors',
	  'add_new' => 'Add Author',
	  'add_new_item' => 'Add New Author',
	  'edit' => 'Edit',
	  'edit_item' => 'Edit Author',
	  'new_item' => 'New Author',
	  'view' => 'View Author',
	  'view_item' => 'View Author',
	  'search_items' => 'Search Authors',
	  'not_found' => 'No Authors Found',
	  'not_found_in_trash' => 'No Authors Found in Trash',
	  'parent' => 'Parent Author',
		)
		)
	  ); 
	}


function getvotescount($post_id)
{
	global $wpdb;
	$count_posts = $wpdb->get_var( "SELECT COUNT(*) FROM wp_votes WHERE post_id=$post_id");
	return $count_posts;
	
}

function new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');


function custom_excerpt_length( $length ) {
	return 150;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function getauthorpostscount($author)
{
	global $wpdb;
	$count_posts = $wpdb->get_var( "SELECT COUNT(*) FROM wp_votes WHERE post_id=$post_id");
	return $count_posts;
	
}

function getBooksByPostTags($post_id)
{
	$tag_info = array();
	$posttags = get_the_tags($post_id);
	// print_r($posttags);
	if ($posttags) {
	  foreach($posttags as $tag) {
//		print_r($tag);
//		echo $tag->name . ' '.$tag->count; 
		$the_slug = $tag->slug;
		$args=array(
		'name' => $the_slug,
//		'post_type' => 'book',
		'post_type' => 'post',
		'post_status' => 'publish',
		'numberposts' => 1
		);
		$my_posts = get_posts($args);
		if( $my_posts ) {
		$tag_info['book_id'] = $my_posts[0]->ID;
		$tag_info['count'] = $tag->count;
		
		return $tag_info;
		}
		
	  }
	  
	  /*if($tag_info['book_id']<='0' || $tag_info['book_id']=='')
	  {
		
	  }*/
	}
}

function getNoOfBooksByAuthorId($author)
{
	$args = array(
	'numberposts' => -1,
	'post_type' => 'book',
	'meta_query'  	=> array(
			      array(
				     'key'           => 'book_author',
				     'value'         => '"'.$author.'"',//quotes to make sure category 23 does not match category 123, 230 etc
				     'compare'       => 'LIKE'
			      )
			   )
	
	);

	// get results
	$the_query = new WP_Query( $args );	
	return $the_query->post_count;
	wp_reset_query();
	
}

function getNoOfPostsByAuthorId($author)
{
	$sql = "select count(*) from wp_posts p RIGHT JOIN wp_postmeta pm ON  p.ID=pm.post_id WHERE p.post_type='post' AND meta_key='tidal_contributor' AND meta_value='$author'";

	global $wpdb;
	$count_posts = $wpdb->get_var( $sql );
	return $count_posts;
	
}

function getPostsByAuthorId($author)
{
	$sql = "select p.ID from wp_posts p RIGHT JOIN wp_postmeta pm ON  p.ID=pm.post_id WHERE p.post_type='post' AND meta_key='tidal_contributor' AND meta_value='$author'";

	global $wpdb;
	$results = $wpdb->get_results( $sql );
	return $results;
	
}

function getAvonStars(){
	$sql = "select pm.meta_value as username, count(*) AS no_of_posts  from wp_posts p LEFT JOIN wp_postmeta pm ON p.ID=pm.post_id WHERE p.post_type='post' AND pm.meta_key='tidal_contributor' GROUP BY meta_value ORDER BY no_of_posts DESC, username ASC LIMIT 4";
	
	global $wpdb;
	$results = $wpdb->get_results( $sql );
	return $results;
}

function getMostBelovedStory()
{
	$sql = "select p.ID, p.post_title, p.post_name, p.post_date, count(*) AS loves from wp_votes v JOIN wp_posts p ON v.post_id=p.ID WHERE p.post_type='post' GROUP BY post_id ORDER BY loves DESC LIMIT 1";
	
	global $wpdb;
	$results = $wpdb->get_results( $sql );
	return $results;
}


function TalkingAboutCount($post_name)
{
	$sql = "select count(*) from wp_posts p, wp_postmeta pm WHERE p.ID=pm.post_id AND p.post_type='post' AND (p.post_title LIKE '%$post_name%' OR p.post_content LIKE '%$post_name%')";
	
	global $wpdb;
	$count_posts = $wpdb->get_var( $sql );
	return $count_posts;
}

function getBookIdsSortByAuthor()
{
	$sql = "SELECT books.ID FROM (select p.*, pm.meta_key, pm.meta_value,  SUBSTRING_INDEX(SUBSTRING_INDEX(pm.meta_value,'\"',2),'\"',-1) AS author_id from wp_posts p LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id WHERE p.post_type='book' AND pm.meta_key='book_author') AS books LEFT JOIN wp_posts p2 ON books.author_id = p2.ID ORDER BY p2.post_title ASC";
	
	global $wpdb;
	$results = $wpdb->get_results( $sql );
	return $results;
}
// NOTE: Modified Stephen La Pierre 10/30/14. At 3:57 PM
function isbn2static($pisbn) {
	$isbn = trim($pisbn);
	$last = $isbn[12];
	$answer = 'http://static.harpercollins.com/harperimages/isbn/large/' . $last . '/' . $isbn . '.jpg';
	return $answer;
}

?>
