<?php
/* Template name: Search */
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header();
$search_term = esc_attr($_GET['q']);
$view = esc_attr($_GET['view']);

function title_filter( $where, &$wp_query )
{
	global $wpdb;
	if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
		$where .= ' AND wp_posts.post_title LIKE \'%' .  $search_term. '%\' AND wp_posts.post_parent = 0';
//		$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\'';
	}
	return $where;
}

function title_and_content_filter( $where, &$wp_query )
{
	global $wpdb;
	if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
//	$where .= ' AND wp_posts.post_title LIKE \'%' .  $search_term . '%\' AND wp_posts.post_parent = 0 AND wp_posts.post_title NOT LIKE "%review%" ';
//	$where .= ' AND wp_posts.post_title LIKE \'%' .  $search_term . '%\' OR wp_posts.post_content LIKE \'%' . $search_term . '%\' AND wp_posts.post_parent = 0 AND wp_posts.post_name NOT LIKE %review% ';
		$where .= ' AND (' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\')';
//		$where .= ' OR ' . $wpdb->posts . '.post_content LIKE \'%' . esc_sql( like_escape( $search_term ) ) . '%\' )';
	}
//	print_r($where);
	return $where;
}

function review_title_and_content_filter( $where, &$wp_query )
{
	global $wpdb;
	if ( $search_term = $wp_query->get( 'search_prod_title' ) ) {
	$where .= ' AND wp_posts.post_title LIKE \'%' .  $search_term . '%\' OR wp_posts.post_content LIKE \'%' . $search_term . '%\' AND wp_posts.post_parent = 0';
	}
//	print_r($where);
	return $where;
}
		
if($view == ''){
if($search_term != ''){
?>
	<div class="span8 community">
		<div class="floral">
			<h3 style="margin-right:20px" class="pull-left">Book Results</h3>
			<ul>
			<li><a href="/search?q=<?php echo $search_term;?>&view=books">See All &raquo;</a></li>
			</ul>
		</div>
		
		<?php
		wp_reset_query();
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		
		$args = array(
		'numberposts' => -1,
		'post_type' => 'book',
		'post_status' => 'publish',
		'showposts' => 3,
		'paged' => $paged,
		'orderby' => 'date',
		'order' => 'DESC',
		'search_prod_title' => $search_term
		);
		add_filter( 'posts_where', 'title_filter', 10, 2 );
//		add_filter( 'posts_where', 'title_and_content_filter', 10, 2 );
		$query_books = new WP_Query( $args );
//		echo $query_books->request;
		remove_filter( 'posts_where', 'title_filter', 10, 2 );
//		remove_filter( 'posts_where', 'title_and_content_filter', 10, 2 );

 
//$query_books = new WP_Query( $args );
		
		$catname = 'book';
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_field('book_author');
//			$book_author = getAuthorName($post->ID);
//			$talking_cnt = TalkingAboutCount($book_author[0]->post_title);
			$talking_cnt = TalkingAboutCount(get_the_title());
$aid = getAuthorID($post->ID);
$author = getAuthorNameByID($aid);
				
			// SLAP add
			$cover = getStaticCover($post->ID);
			if(strlen($cover) < 13)
				$cover = $book_custom_fields['cover_photo_url'][0];
				
			if($cover !='' && isset($cover)){ 		
		?>
		<div class="book <?php if($book_counter%3 == 0){echo "last";}?>">
<!--			<a href="<?php the_permalink(); ?>"><img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>"></a> -->
			<a href="<?php the_permalink(); ?>"><img class="book-180" src="<?php echo $cover; ?>" ></a>

			<ul class="info">
				<li><a href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
				<li><a href="<?php echo get_permalink((int)$book_author[0]); ?>"><?php echo $author; ?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
				<?php if($book_custom_fields['genre'][0]!='' && $book_custom_fields['genre'][0]!='null'){ ?>
				<li><a href="/books/genre/1?genre=<?php echo $book_custom_fields['genre'][0]; ?>">See more in <?php echo ucfirst($book_custom_fields['genre'][0]); ?></a></li>
				<?php } ?>
			</ul>
		</div>
		 <?php 
		 $book_counter++;
		 }
		 endwhile;
		else : ?>
		<p style="margin-bottom:50px"><em>We couldn't find any books similar to "<?php echo $search_term;?>".</em></p>
		<?php endif; ?>	
		<div class="clear"></div>
		<h2 class="prev-page"><?php previous_posts_link('&laquo; Previous') ?></h2>
		<h2 class="next-page"><?php next_posts_link('Next &raquo;', $query_books->max_num_pages) ?></h2>
		<div class="clear"></div>
		<?php wp_reset_postdata(); ?>
		
		
		<div style="margin-top:20px" class="floral">
		<h3 style="margin-right:20px" class="pull-left">Author Results</h3>
		<ul>
		<li><a href="/search?q=<?php echo $search_term;?>&view=authors">See All &raquo;</a></li>
		</ul>
		</div>
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		// The Author Search is here
		$args = array(
		'numberposts' => -1,
		'post_type' => 'book-author',
		'showposts' => 3,
		'paged' => $paged,
		'orderby' => 'date',
		'order' => 'DESC',
		'search_prod_title' => $search_term,
		);
		add_filter( 'posts_where', 'title_filter', 10, 2 );
		// get results
		$query_books = new WP_Query( $args );
		remove_filter( 'posts_where', 'title_filter', 10, 2 );
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($query_books->post->ID);
//			$book_author = get_the_title();
// 			$book_author = get_field('book_author');
// 			$authorID = (int)$book_author[0];
//echo "authorid author = " . $query_books->post->ID . PHP_EOL;
//echo $book_author;
			$author = getAuthorNameByID($query_books->post->ID);
			
// 			$find = "author:" . $book_author;
//			$no_of_books = getNoOfBooksByAuthorId($author);
//			$no_of_books = getNoOfBooksByAuthorId($query_books->post->ID);
//			$no_of_books = getNoOfBooksByAuthorId($author);
//			$no_of_books = getNoOfBooksByAuthorId($post->ID);
			$authGid = getAuthorGID($post->ID);
			$no_of_books = getNoOfBooksByAuthorGID($authGid);
//echo "HERE IS ERROR";
		?>
		<div class="profile <?php if($book_counter%3 == 0){echo "last";}?>">
			<a href="<?php the_permalink(); ?>" class="image">
			<?php if($book_custom_fields['profile_image'][0] != '') { ?>
				<img src="<?php echo $book_custom_fields['profile_image'][0]; ?>">
				<?php } else { ?>
				<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png"><?php } ?>
				<div class="badge-author"></div>
			</a>
			<ul class="info">
				<!-- <li><a href="<?php the_permalink(); ?>?view=books&q=<?php echo esc_html($book_author);?>">View Books</a></li>
				<li><a href="<?php the_permalink(); ?>?view=books&q=<?php echo esc_html($book_author);?>"> Books</a></li>	-->
				<li><a href="<?php the_permalink(); ?>"><?php echo $author;?></a></li>
				<li><a href="<?php the_permalink(); ?>?view=books&q=<?php echo esc_html($query_books->post->post_title);?>">View Books</a></li>
				<li><a href="<?php the_permalink(); ?>?view=books&q=<?php echo esc_html($query_books->post->post_title);?>">Books</a></li>
			</ul>
		</div>
		 <?php 
		 $book_counter++;
		 endwhile;
		 ?>
		 <div class="clear"></div>
		 <?php
			
		else : ?>
		<p style="margin-bottom:50px"><em>We couldn't find any authors similar to "<?php echo $search_term;?>".</em></p>
		<?php endif; ?>	

		<div class="clear"></div>
		<div style="margin-top:20px" class="floral">
			<h3 style="margin-right:20px" class="pull-left">Post Results</h3>
			<ul>
			<li><a href="/search?q=<?php echo $search_term;?>&view=posts">See All &raquo;</a></li>
			</ul>
		</div>
		
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$args = array(
		'numberposts' => -1,
		'post_type' => 'post',
		'showposts' => 2,
		'paged' => $paged,
		'orderby' => 'date',
		'order' => 'DESC',
		'search_prod_title' => $search_term,
		);
		add_filter( 'posts_where', 'title_filter', 10, 2 );
		// get results
		$my_query = new WP_Query( $args );
		remove_filter( 'posts_where', 'title_filter', 10, 2 );
		$book_counter=1;
		
		if( $my_query->have_posts() ) {
	while ($my_query->have_posts()) : $my_query->the_post(); 
	
	
	$book_tag_info = getBooksByPostTags($post->ID);	
	$book_id = $book_tag_info['book_id'];
	$book_tag_count = $book_tag_info['count'];
	
	$book_info = get_post($book_id);
	$book_custom_fields = get_post_custom($post->ID);
//	print_r($book_custom_fields);
	$post_vote_count = getvotestypecount($post->ID, 'p');
	$book_vote_count = getvotestypecount($post->ID, 'b');
	
	$post_book_author = get_field('book_author', $book_id);
	?>
	<div class="post">
		<div class="group bubble" style="background: none repeat scroll 0% 0% rgb(246, 127, 167);">
			<h2><strong><?php the_time('M d') ?></strong> - Book Review</h2>
			<div class="vote">
			<h2 class="pull-left">
			<a href="#" data-slug="<?php echo $post->ID;?>" class="vote-link">Love this post! <span data-slug="<?php echo $post->ID;?>" class="vote-count"><?php echo $post_vote_count;?></span> | &hearts;</a>
			</h2>
			<?php if($book_id!='' && $book_id!='0'){ ?>
			<h2 class="pull-right">
			<a href="#" data-slug="<?php echo $book_id;?>" class="book-vote-link">Love this book! <span data-slug="<?php echo $book_id;?>" class="book-vote-count"><?php echo $book_vote_count;?></span> | &hearts;</a>
			</h2>
			<?php } ?>
			</div>
		</div>
		<div class="span5">
			<h1>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h1>
			<div class="social"></div>
			<?php the_excerpt(); ?>	
			<p><a href="<?php the_permalink(); ?>"><i>read more</i></a></p>
			<div class="small" id="2ae284d637ece070fb33"></div>
			
			<div class="tags">						
				<?php the_tags( '<h5>Tags</h5><ul><li>', '</li><li>', '</li></ul>' ); ?>						
			</div>
		</div>
		<div class="span3">
			<div class="profile">
			<?php
			$author_user = get_the_author(); 
			$author_obj = get_page_by_title( $author_user, OBJECT, 'tidal_contributor' ); 
			$custom_fields = get_post_custom($author_obj->ID);
			$author_data = unserialize($custom_fields['badges'][0]);
			//print_r($author_user);
			$no_of_posts = getNoOfPostsByAuthorId($author_obj->post_name);
			
			?> 
			<h5 class="hidden">Contributor:</h5>
			<div class="badge-full">
			<div class="badge-addict">
			<?php if($author_data[1]['image']!=''){ ?>
			<img src="<?php echo $author_data[1]['image']; ?>">
			<?php } else{ ?>
			<img src="http://cdna.tid.al/e1990e097fb1829c98ffe5e74df489a2cc01b1d0_100.png">
			<?php } ?>
			</div>
			<?php if($custom_fields['profile_image'][0]!=''){ ?>
			<img src="<?php echo $custom_fields['profile_image'][0]; ?>">
			<?php } else{ ?>
			<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png">
			<?php } ?>
			</div>
			<ul class="info">
			<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php the_author(); ?></a></li>
			<!-- SLAP fix for no value to get -->
			<?php if($book_custom_fields['blog_name'][0]!='' && $book_custom_fields['blog_name'][0]!='null'){ ?>
			<?php foreach($custom_fields['blog_name'] as $authorblog => $authorbloginfo){ ?>
			<li><a href="<?php echo $custom_fields['blog_url'][$authorblog]; ?>" target="_blank"><?php echo $authorbloginfo;?></a></li>
			<?php } ?>
				<?php } ?>
			<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php echo $no_of_posts; ?> Posts</a></li>
			</ul>
			</div>
			<div>
			<?php 
			
	
			if($book_id !='' && $book_id !='0'){ 
			$talking_cnt = TalkingAboutCount($book_info->post_title);
			?>
			<h5 class="hidden">Book:</h5>
			<a href="<?php echo get_permalink($book_id); ?>">
			<img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>" style="width:100%">
			</a>
			<ul class="info">
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_info->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($post_book_author[0]->ID); ?>; ?>">By <?php echo $post_book_author[0]->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
			
			</ul>
			<?php } ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php
	endwhile;
	}else{ ?>
	<p style="margin-bottom:20px"><em>No posts were found.</em></p>
	<?php } 
	wp_reset_query();  // Restore global post data stomped by the_post().
	?>
		
		
	</div>
	<?php } else { ?>
	<div class="span8 community">	
		<div style="margin-top:20px" class="floral">
		<h3 style="margin-right:20px" class="pull-left">Post Results</h3>
		<ul>
		<li><a href="/search?q=<?php echo $search_term;?>&view=posts">See All &raquo;</a></li>
		</ul>
		</div>
		<p style="margin-bottom:20px"><em>No posts were found.</em></p>	
		<div class="clear"></div>
		<div class="clear"></div>
	</div>
	<?php } ?>
<?php } else if($view != ''){
?>
<div class="span8 community">
<div class="floral">
	<h3 style="margin-right:20px" class="pull-left">Search Results</h3>
	<ul>
	<li><a href="/search?q=<?php echo $search_term;?>&view=posts" <?php if($view=='posts'){ ?>class="active"<?php } ?>>Posts</a></li>
	<li><a href="/search?q=<?php echo $search_term;?>&view=books" <?php if($view=='books'){ ?>class="active"<?php } ?>>Books</a></li>
	<li><a href="/search?q=<?php echo $search_term;?>&view=authors" <?php if($view=='authors'){ ?>class="active"<?php } ?>>Authors</a></li>
	</ul>
</div>

<?php
		if($view=='books'){
		$paged = ($_GET['sort']) ? $_GET['sort'] : 1;
		
		
//		echo "Here I am : " . $search_term;
// 		$max_args = array(
// 		'numberposts' => -1,
// 		'post_type' => 'book',
// //		'post_type' => 'post',
// 		'orderby' => 'date',
// 		'order' => 'DESC',
// 		'search_prod_title' => $search_term,
// 		);
// 		add_filter( 'posts_where', 'title_filter', 10, 2 );
// 		// get results
// 		$query_max_books = new WP_Query( $max_args );
// 		remove_filter( 'posts_where', 'title_filter', 10, 2 );
// 	echo "BOOK SEARCH TIME" . PHP_EOL;
// 	echo $query_books->post->ID;
// 	echo $search_term;
//	echo "In books-author";
	$sql = 'select ID from wp_posts p WHERE p.post_type="book-author" AND p.post_title="' . $search_term . '" AND p.post_parent=0';
	global $wpdb;
//	$authid = $wpdb->get_var( 'select ID from wp_posts p WHERE p.post_type="book-author" AND p.post_title=$search_term AND p.post_parent=0');
	$authid = $wpdb->get_var( $sql );
//	echo $wpdb->last_query;
 	wp_reset_query();
	$sql = 'select meta_value from wp_postmeta pm WHERE pm.meta_key="Author_GID" AND pm.post_id=' . $authid;
	$gid = $wpdb->get_var( $sql );
 	wp_reset_query();
// 	echo $gid;

// 	$args = array(
// 	'numberposts' => -1,
// 	'post_type' => 'book',
// 	'meta_query'  	=> array(
// 			      array(
// 				     'key'           => 'book_author',
// 				     'value'         => serialize($authid),//quotes to make sure category 23 does not match category 123, 230 etc
// 				     'compare'       => 'LIKE'
// 			      )
// 			   )
// 	
// 	);
 $args = array(
	'numberposts' => -1,
	'post_type' => 'book',
	'meta_query'  	=> array(
			      array(
				     'key'           => 'Author_GID',
				     'value'         => $gid,//quotes to make sure category 23 does not match category 123, 230 etc
				     'compare'       => '='
			      )
			   )
	
	);

		
// 		$args = array(
// 		'numberposts' => -1,
// 		'post_type' => 'book',
// //		'post_type' => 'post',
// 		'showposts' => 21,
// 		'paged' => $paged,
// 		'orderby' => 'date',
// 		'order' => 'DESC',
// 		'search_prod_title' => $search_term,
// 		);
// 		add_filter( 'posts_where', 'title_filter', 10, 2 );
// 		// get results
 		$query_books = new WP_Query( $args );
// 		remove_filter( 'posts_where', 'title_filter', 10, 2 );
//	echo $wpdb->last_query;
//	echo serialize($authid);
		
//		echo "374";
		
		$catname = 'book';
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_field('book_author');
// 			var_dump( $book_author);
//			$book_author = get_field('book_author');
//			$book_author = getAuthorName($query_books->$post->ID);
			$authorID = (int)$book_author[0];
//			$book_author = getAuthorName($authorID);
			$author = getAuthorNameByID($book_author[0]);
			$talking_cnt = TalkingAboutCount(get_the_title());
		?>
		<div class="book <?php if($book_counter%3 == 0){echo "last";}?>">
			<a href="<?php the_permalink(); ?>"><img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>"></a>
			<ul class="info">
				<li><a href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
			<!--<li><a href="<?php echo get_permalink($book_author[0]->ID); ?>"><?php echo $book_author[0]->post_title; ?></a></li> -->
					<li><a href="<?php echo esc_html($author); ?>"><?php echo $author; ?></a></li> 
				<li><a href="<?php the_permalink(); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
				<?php if($book_custom_fields['genre'][0]!='' && $book_custom_fields['genre'][0]!='null'){ ?>
				<li><a href="/books/genre/1?genre=<?php echo $book_custom_fields['genre'][0]; ?>">See more in <?php echo ucfirst($book_custom_fields['genre'][0]); ?></a></li>
				<?php } ?>
			</ul>
		</div>
		 <?php 
		 $book_counter++;
		 endwhile;
		else : ?>
		<p style="margin-bottom:50px"><em>No books were found.</em></p>
		<?php endif; ?>	
		<div class="clear"></div>
		<?php
		$pager = ($_GET['sort']) ? $_GET['sort'] : 1;
		//$pager=$_GET['sort'];
		
		$max_posts = $query_max_books->post_count;
		$max_pages = ceil($max_posts/21);
		?>
		<?php if($pager>1 && $pager<=$max_pages) { ?>
		<h2 class="prev-page"><a href="/search?q=<?php echo $search_term;?>&view=books&sort=<?php echo $pager-1;?>">&laquo; Previous</a></h2>
		<?php } ?>
		<?php if($pager>=1 && $pager<$max_pages) { ?>
		<h2 class="next-page"><a href="/search?q=<?php echo $search_term;?>&view=books&sort=<?php echo $pager+1;?>">Next &raquo;</a></h2>
		<?php } ?>
		<div class="clear"></div>
		<?php wp_reset_postdata(); ?>
		<?php } ?>
		
		<?php 
		if($view=='authors'){
		$paged = ($_GET['sort']) ? $_GET['sort'] : 1;
		
		$max_args = array(
		'numberposts' => -1,
		'post_type' => 'book-author',
		'orderby' => 'date',
		'order' => 'DESC',
		'search_prod_title' => $search_term,
		);
		add_filter( 'posts_where', 'title_filter', 10, 2 );
		// get results
		$query_max_books = new WP_Query( $max_args );
		remove_filter( 'posts_where', 'title_filter', 10, 2 );
//		echo "Here in Author : " . $search_term;
		$args = array(
		'numberposts' => -1,
		'post_type' => 'book-author',
		'showposts' => 21,
		'paged' => $paged,
		'orderby' => 'date',
		'order' => 'DESC',
		'search_prod_title' => $search_term,
		);
		add_filter( 'posts_where', 'title_filter', 10, 2 );
		// get results
		$query_books = new WP_Query( $args );
		remove_filter( 'posts_where', 'title_filter', 10, 2 );
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_the_title();
			$author = getAuthorNameByID($query_books->post->ID);
//			echo $book_author;
			$pbase1 = get_the_permalink();
			$authGid = getAuthorGID($post->ID);
			$no_of_books = getNoOfBooksByAuthorGID($authGid);
//			$no_of_books = getNoOfBooksByAuthorId($post->ID);
//			echo "Her I am";
// below author name changed from the_title() to $author and $pbase1 for the_permalink

		?>
		<div class="profile <?php if($book_counter%3 == 0){echo "last";}?>">
			<a href="<?php echo $pbase1; ?>" class="image">
			<?php if($book_custom_fields['profile_image'][0] != '') { ?>
				<img src="<?php echo $book_custom_fields['profile_image'][0]; ?>">
				<?php } else { ?>
				<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png"><?php } ?>
				<div class="badge-author"></div>
			</a>
			<ul class="info">
				<li><a href="<?php echo $pbase1; ?>"><?php echo $author;?></a></li>
				<li><a href="<?php the_permalink(); ?>?view=books">View Books</a></li>
				<li><a href="<?php the_permalink(); ?>?view=books">Books</a></li>
			</ul>
		</div>
		 <?php 
		 $book_counter++;
		 endwhile;
		 else:
		 ?>
		 <p style="margin-bottom:50px"><em>No authors were found.</em></p>
		 <?php endif;
		 ?>
	
		<div class="clear"></div>
		<?php
		$pager = ($_GET['sort']) ? $_GET['sort'] : 1;
		//$pager=$_GET['sort'];
		
		$max_posts = $query_max_books->post_count;
		$max_pages = ceil($max_posts/21);
		?>
		<?php if($pager>1 && $pager<=$max_pages) { ?>
		<h2 class="prev-page"><a href="/search?q=<?php echo $search_term;?>&view=authors&sort=<?php echo $pager-1;?>">&laquo; Previous</a></h2>
		<?php } ?>
		<?php if($pager>=1 && $pager<$max_pages) { ?>
		<h2 class="next-page"><a href="/search?q=<?php echo $search_term;?>&view=authors&sort=<?php echo $pager+1;?>">Next &raquo;</a></h2>
		<?php } ?>
		<div class="clear"></div>
		<?php wp_reset_postdata(); ?>
		<?php } ?>
		
		
		
		<?php 
		if($view=='posts'){
		$paged = ($_GET['sort']) ? $_GET['sort'] : 1;
		
		$max_args = array(
		'numberposts' => -1,
		'post_type' => 'post',
		'orderby' => 'date',
		'order' => 'DESC',
		'search_prod_title' => $search_term,
		);
		add_filter( 'posts_where', 'title_filter', 10, 2 );
		// get results
		$query_max_posts = new WP_Query( $max_args );
		remove_filter( 'posts_where', 'title_filter', 10, 2 );
		
		$args = array(
		'numberposts' => -1,
		'post_type' => 'post',
		'showposts' => 9,
		'paged' => $paged,
		'orderby' => 'date',
		'order' => 'DESC',
		'search_prod_title' => $search_term,
		);
		add_filter( 'posts_where', 'title_filter', 10, 2 );
		// get results
		$my_query = new WP_Query( $args );
//		echo $my_query->request;
		remove_filter( 'posts_where', 'title_filter', 10, 2 );
		$book_counter=1;
		
		if( $my_query->have_posts() ) {
	while ($my_query->have_posts()) : $my_query->the_post(); 
	
	
	$book_tag_info = getBooksByPostTags($post->ID);	
	$book_id = $book_tag_info['book_id'];
	$book_tag_count = $book_tag_info['count'];
	
	$book_info = get_post($book_id);
	//print_r($book_info);
	$book_custom_fields = get_post_custom($book_id);
	//print_r($book_custom_fields);
	$post_vote_count = getvotestypecount($post->ID , 'p');
	$book_vote_count = getvotestypecount($post->ID, 'b');
	
	$post_book_author = get_field('book_author', $book_id);
//	echo "549";

	?>
	<div class="post">
		<div class="group bubble" style="background: none repeat scroll 0% 0% rgb(246, 127, 167);">
			<h2><strong><?php the_time('M d') ?></strong> - Book Review</h2>
			<div class="vote">
			<h2 class="pull-left">
			<a href="#" data-slug="<?php echo $post->ID;?>" class="vote-link">Love this post! <span data-slug="<?php echo $post->ID;?>" class="vote-count"><?php echo $post_vote_count;?></span> | &hearts;</a>
			</h2>
			<?php if($book_id!='' && $book_id!='0'){ ?>
			<h2 class="pull-right">
			<a href="#" data-slug="<?php echo $book_id;?>" class="book-vote-link">Love this book! <span data-slug="<?php echo $book_id;?>" class="book-vote-count"><?php echo $book_vote_count;?></span> | &hearts;</a>
			</h2>
			<?php } ?>
			</div>
		</div>
		<div class="span5">
			<h1>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h1>
			<div class="social"></div>
			<?php the_excerpt(); ?>	
			<p><a href="<?php the_permalink(); ?>"><i>read more</i></a></p>
			<div class="small" id="2ae284d637ece070fb33"></div>
			
			<div class="tags">						
				<?php the_tags( '<h5>Tags</h5><ul><li>', '</li><li>', '</li></ul>' ); ?>						
			</div>
		</div>
		<div class="span3">
			<div class="profile">
			<?php
			$author_user = get_the_author(); 
			$author_obj = get_page_by_title( $author_user, OBJECT, 'tidal_contributor' ); 
			$custom_fields = get_post_custom($author_obj->ID);
			$author_data = unserialize($custom_fields['badges'][0]);
			//print_r($author_user);
			$no_of_posts = getNoOfPostsByAuthorId($author_obj->post_name);
			
			?> 
			<h5 class="hidden">Contributor:</h5>
			<div class="badge-full">
			<div class="badge-addict">
			<?php if($author_data[1]['image']!=''){ ?>
			<img src="<?php echo $author_data[1]['image']; ?>">
			<?php } else{ ?>
			<img src="http://cdna.tid.al/e1990e097fb1829c98ffe5e74df489a2cc01b1d0_100.png">
			<?php } ?>
			</div>
			<?php if($custom_fields['profile_image'][0]!=''){ ?>
			<img src="<?php echo $custom_fields['profile_image'][0]; ?>">
			<?php } else{ ?>
			<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png">
			<?php } ?>
			</div>
			<ul class="info">
			<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php the_author(); ?></a></li>
			<?php foreach($custom_fields['blog_name'] as $authorblog => $authorbloginfo){ ?>
			<li><a href="<?php echo $custom_fields['blog_url'][$authorblog]; ?>" target="_blank"><?php echo $authorbloginfo;?></a></li>
			<?php } ?>
			<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php echo $no_of_posts; ?> Posts</a></li>
			</ul>
			</div>
			<div>
			<?php 
			
	
			if($book_id !='' && $book_id !='0'){ 
			$talking_cnt = TalkingAboutCount($book_info->post_title);
			?>
			<h5 class="hidden">Book:</h5>
			<a href="<?php echo get_permalink($book_id); ?>">
			<img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>" style="width:100%">
			</a>
			<ul class="info">
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_info->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($post_book_author[0]->ID); ?>; ?>">By <?php echo $post_book_author[0]->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
			
			</ul>
			<?php } ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php
	endwhile;
	}
	else{?>
	<p style="margin-bottom:50px"><em>No posts were found.</em></p>
	<?php } ?>
	
		<?php
		$pager = ($_GET['sort']) ? $_GET['sort'] : 1;
		//$pager=$_GET['sort'];
//		echo "646";

		$max_posts = $query_max_posts->post_count;
		$max_pages = ceil($max_posts/9);
		?>
		<?php if($pager>1 && $pager<=$max_pages) { ?>
		<h2 class="prev-page"><a href="/search?q=<?php echo $search_term;?>&view=posts&sort=<?php echo $pager-1;?>">&laquo; Previous</a></h2>
		<?php } ?>
		<?php if($pager>=1 && $pager<$max_pages) { ?>
		<h2 class="next-page"><a href="/search?q=<?php echo $search_term;?>&view=posts&sort=<?php echo $pager+1;?>">Next &raquo;</a></h2>
		<?php } ?>
		<div class="clear"></div>
		<?php wp_reset_postdata(); ?>
		<?php } ?>
		
		
		
</div>
<?php } ?>
<div class="span4">
<?php get_sidebar();?>
</div>	

<div class="cf"></div>
<?php get_footer(); ?>
