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

add_action('init', 'cptui_register_my_cpt_book');
function cptui_register_my_cpt_book() {
register_post_type('book', array(
'label' => 'Books',
'description' => '',
'public' => true,
'show_ui' => true,
'show_in_menu' => true,
'capability_type' => 'post',
'map_meta_cap' => true,
'hierarchical' => false,
'rewrite' => array('slug' => 'book', 'with_front' => true),
'query_var' => true,
'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
'labels' => array (
  'name' => 'Books',
  'singular_name' => 'Book',
  'menu_name' => 'Books',
  'add_new' => 'Add Book',
  'add_new_item' => 'Add New Book',
  'edit' => 'Edit',
  'edit_item' => 'Edit Book',
  'new_item' => 'New Book',
  'view' => 'View Book',
  'view_item' => 'View Book',
  'search_items' => 'Search Books',
  'not_found' => 'No Books Found',
  'not_found_in_trash' => 'No Books Found in Trash',
  'parent' => 'Parent Book',
)
) ); }

function getvotescount($post_id)
{
	global $wpdb;
	$count_posts = $wpdb->get_var( "SELECT COUNT(*) FROM wp_votes WHERE post_id=$post_id");
	return $count_posts;
	
}

function getvotestypecount($post_id, $type)
{
	global $wpdb;
	$count_posts = $wpdb->get_var( "SELECT COUNT(*) FROM wp_votes WHERE post_id=$post_id AND post_type='$type'");
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

function getPostsByTag($tag)
{
	global $post;
	$postsByTag = get_posts('tag=$tag');
   return $postsByTag->post_count;
}

function getBooksByPostTags($post_id)
{
	$tag_info = array();
	$posttags = get_the_tags($post_id);
	if ($posttags) {
	  foreach($posttags as $tag) {
		$the_slug = $tag->slug;
		$args=array(
		'name' => $the_slug,
		'post_type' => 'book',
		'post_status' => 'publish',
//		'numberposts' => 1
		'posts_per_page' => 1
		);
		$my_posts = get_posts($args);
		if( $my_posts ) {
		$tag_info['book_id'] = $my_posts[0]->ID;
		$tag_info['count'] = $tag->count;
		
		return $tag_info;
		} else {
		}
		
	  }
	  
	  /*if($tag_info['book_id']<='0' || $tag_info['book_id']=='')
	  {
		
	  }*/
	}
}

function getNoOfBooksByAuthorGID($gid)
{
   $args = array(
      'post_type' => 'book',
      'meta_query' => array(
         array(
            'key' => 'Author_GID',
            'value' => $gid,
            'compare' => '='
         )
      )
   );
   $books_posts = new WP_Query($args);
   return $books_posts->post_count;
}
function getAuthorGID($postid)
{
	global $wpdb;
	$sql = "select meta_value from wp_postmeta where meta_key = 'Author_GID' and post_id = $postid";
	$agid = $wpdb->get_var( $sql );
	return $agid;
}

function getNoOfBooksByAuthorId($author)
{
	$sql = 'select ID from wp_posts p WHERE p.post_type="book-author" AND p.post_title="' . $author . '" AND p.post_parent=0';
	global $wpdb;
	$authid = $wpdb->get_var( $sql );
	$authidarr = array((string)$authid);
 	wp_reset_query();

	$args = array(
//	'numberposts' => -1,
	'post_type' => 'book',
	'meta_query'  	=> array(
			      array(
				     'key'           => 'book_author',
//				     'key'           => 'tidal_contributor',
				     'value'         => serialize($authidarr),//quotes to make sure category 23 does not match category 123, 230 etc
//				     'value'         => '"'.$author.'"',//quotes to make sure category 23 does not match category 123, 230 etc
				     'compare'       => 'LIKE'
			      )
			   )
	
	);

	// get results
	$the_query = new WP_Query( $args );	
	return $the_query->post_count;

}

function getNoOfPostsByAuthorId($author)
{
	$sql = "select count(*) from wp_posts p RIGHT JOIN wp_postmeta pm ON  p.ID=pm.post_id WHERE p.post_type='post' AND meta_key='tidal_contributor' AND meta_value='$author'";

	global $wpdb;
	$count_posts = $wpdb->get_var( $sql );
	return $count_posts;
	
}

function getPostsByAuthorName($author)
{
	$sql = "select p.ID from wp_posts p RIGHT JOIN wp_postmeta pm ON  p.ID=pm.post_id WHERE p.post_type='post' AND meta_key='tidal_contributor' AND meta_value='$author'";

	global $wpdb;
	$results = $wpdb->get_results( $sql );
	return $results->post_count;
	
}

function countPostsByAuthorName($postid)
{
	global $wpdb;
	$sql = "select meta_value from wp_postmeta where meta_key = 'tidal_contributor' and post_id = $postid";
	$aname = $wpdb->get_var( $sql );

	wp_reset_query();
	$sql = "select count(*) from wp_posts p RIGHT JOIN wp_postmeta pm ON  p.ID=pm.post_id WHERE p.post_type='post' AND meta_key='tidal_contributor' AND meta_value='$aname'";

	$count = $wpdb->get_var( $sql );
	return $count;
	
}

function getPostTidalContributor($author)
{
$args = array(
	'post_type' => 'post',
	'meta_query' => array(
		array(
			'key' => 'tidal_contributor',
			'value' => $author,
			'compare' => '=',
		)
	)
 );
 $query = new WP_Query( $args );
 return $query;
}

function getPostsByAuthorID($aid)
{
	$args = array(
//	'numberposts' => -1,
	'post_type' => 'book',
	'meta_query'  	=> array(
			      array(
				     'key'           => 'book_author',
				     'value'         => serialize($authid),//quotes to make sure category 23 does not match category 123, 230 etc
				     'compare'       => 'LIKE'
			      )
			   )
	
	);
 		$query_books = new WP_Query( $args );
		return $query_books->post_count;

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
//	$post_name = esc_sql(esc_like($post_name));
	$post_name = esc_sql($post_name);
//	$sql = "select count(*) from wp_posts p, wp_postmeta pm WHERE p.ID=pm.post_id AND p.post_type='post' AND (p.post_title LIKE '%$post_name%' OR p.post_content LIKE '%$post_name%')";
	$sql = "select count(*) from wp_posts p, wp_postmeta pm WHERE p.ID=pm.post_id AND p.post_type='post' AND (p.post_title LIKE '$post_name' OR p.post_content LIKE '$post_name')";
	
	global $wpdb;
	$count_posts = $wpdb->get_var( $sql );
	return $count_posts;
}

function getBookIdsSortByAuthor()
{
	$sql = "SELECT books.ID FROM (select p.*, pm.meta_key, pm.meta_value,  SUBSTRING_INDEX(SUBSTRING_INDEX(pm.meta_value,'\"',2),'\"',-1) AS author_id from wp_posts p LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id WHERE p.post_type='book' AND pm.meta_key='book_author') AS books LEFT JOIN wp_posts p2 ON books.author_id = p2.ID ORDER BY books.author_id ASC";
//	$sql = "SELECT * FROM wp_posts LEFT JOIN wp_term_relationships ON wp_posts.ID = wp_term_relationships.object_ID LEFT JOIN wp_terms ON wp_terms.term_id = wp_term_relationships.term_taxonomy_id WHERE wp_posts.post_type = 'post'  AND wp_posts.post_status = 'publish' AND wp_posts.post_parent = 0 AND wp_posts.post_name NOT LIKE 'review%' AND wp_terms.name LIKE 'author:%' ORDER BY wp_terms.name";	
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

function getStaticCover($postID)
{
		$cover = "";
		$posttags = get_the_tags($postID);
		if ($posttags) {
		  foreach($posttags as $tag) {
		  $isin = strpos($tag->name, "isbn:");
		  if($isin === 0) {
			$isbn = substr($tag->name, 5);
			$cover = isbn2static($isbn);
			break;
			}
		}
	}			
return $cover;
}
// Get Author Name by Author post ID
function getAuthorNameByID($authid)
{
	if($authid == 0 || $authid===NULL) {
		return "";
	}
	
	global $wpdb;
	$sql = "select p.post_title from wp_posts p WHERE p.ID=$authid";
	$author = $wpdb->get_var( $sql );

	return $author;
}
// Get the author ID from post ID
function getAuthorID($postID)
{
	$sql = "select pm.meta_value from wp_postmeta pm WHERE pm.post_id=$postID AND pm.meta_key='book_author'";
	
	global $wpdb;
	$results = $wpdb->get_results( $sql );
//	var_dump($results);
//	$results = $wpdb->get_var( $sql );
	$authdata = unserialize($results[0]->meta_value);
	return $authdata[0];
}
function getcontribnamebyslug($slug)
{
	if($slug == '' || $slug===NULL) {
		return "";
	}
		global $wpdb;
		$sql = "select post_title from wp_posts WHERE wp_posts.post_name = '$slug' AND post_type = 'tidal_contributor'";
		$author = $wpdb->get_var( $sql );
	return $author;
}
function getnamebyslug($slug)
{
		global $wpdb;
		$sql = "select post_title from wp_posts WHERE wp_posts.post_name = '$slug'";
		$author = $wpdb->get_var( $sql );
	return $author;
}
// Get the Author Name by Post ID
function getAuthorName($postID)
{
	if($postID == 0 || $postID===NULL) {
		return "";
	}
	
	$sql = "select pm.meta_value from wp_postmeta pm WHERE pm.post_id=$postID AND pm.meta_key='book_author'";
	
	global $wpdb;
	$results = $wpdb->get_results( $sql );
	if((count($results) < 1)) {
		return "";
	}
	$authdata = unserialize($results[0]->meta_value);
	if($authdata == '' || $authdata===NULL) {
		return "";
	}
	wp_reset_query();
	$sql = "select post_title from wp_posts WHERE wp_posts.ID=$authdata[0]";
	$author = $wpdb->get_var( $sql );

	return $author;
}
?>
