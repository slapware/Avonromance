<?php /**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
// Display the Author Bio and author books from view books link

get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php $view = $_GET['view']; ?>
<div class="content floral-top">
	<div class="span8">
		<div class="floral bottom">
			<h4><?php the_title(); ?></h4>
			<ul>
			<li><a href="?view=posts" <?php if($view == 'posts'){ echo 'class="active"';}?>>Posts</a></li>
			<li><a href="?view=books" <?php if($view == 'books' || $view == ''){ echo 'class="active"';}?>>Books</a></li>
			</ul>
		</div>
		
		<?php
		$book_custom_fields = get_post_custom($post->ID);
		$authorgid = get_field('Author_GID');
		$bookauthor = the_title("", "", FALSE);
		$author_page_title = $post->post_name;
		if($view == 'books' || $view == ''){
		$find = "author:" . the_title("", "", FALSE);
		$args = array(
		'nopaging' => true,
		'post_type' => 'book',
		'meta_query'  	=> array(
				  array(
					 'key'           => 'Author_GID',
					 'value'         => $authorgid,//quotes to make sure category 23 does not match category 123, 230 etc
					 'compare'       => '='
				  )
			   )

// 		'meta_query'  	=> array(
// 				  array(
// 					 'key'           => 'book_author',
// 					 'value'         => '"'.$post->ID.'"',//quotes to make sure category 23 does not match category 123, 230 etc
// 					 'compare'       => 'LIKE'
// 				  )
// 			   ),
// 		'orderby' => 'date',
// 		'order' => 'DESC'
		);
// org query here
// 		$args = array(
// 		'numberposts' => -1,
// 		'post_type' => 'book',
// //		'post_type' => 'post',
// 		'meta_query'  	=> array(
// 				  array(
// 					 'key'           => 'name',
// 					 'value'         => $find,//quotes to make sure category 23 does not match category 123, 230 etc
// 					 'compare'       => 'LIKE'
// 				  )
// 			   ),
// 
// 		'meta_query'  	=> array(
// 				  array(
// 					 'key'           => 'book_author',
// 					 'value'         => '"'.$post->ID.'"',//quotes to make sure category 23 does not match category 123, 230 etc
// 					 'compare'       => 'LIKE'
// 				  )
// 			   ),
// 		'orderby' => 'date',
// 		'order' => 'DESC'
// 		);
//		$find = "author:" . the_title("", "", FALSE);
		$find = "author" . strtolower ( the_title("", "", FALSE) );
		$slug = str_replace(" ", "-", $find);

		// get results
		$query_books = new WP_Query( $args );

		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_field('book_author');
			
			$talking_cnt = TalkingAboutCount(get_the_title());
			$cover = getStaticCover($post->ID);
//			$bookauthor = getAuthorName($post->ID);

			
		?>
		<div class="book <?php if($book_counter%3 == 0){echo "last";}?>">
<!--			<a href="<?php the_permalink(); ?>"><img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>"></a> -->
			<a href="<?php the_permalink(); ?>"><img src="<?php echo $cover; ?>"></a>
			<ul class="info">
				<li><a href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
<!--				<li><a href="<?php echo get_permalink($book_author[0]->ID); ?>"><?php echo $book_author[0]->post_title; ?></a></li> -->
				<li><a href="<?php echo get_permalink($book_author[0]->ID); ?>"><?php echo $bookauthor; ?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo $talking_cnt;?> People Talking About.</a></li>
				<?php if($book_custom_fields['genre'][0]!='' && $book_custom_fields['genre'][0]!='null'){ ?>
				<li><a href="/books/genre/1?genre=<?php echo $book_custom_fields['genre'][0]; ?>">See more in <?php echo ucfirst($book_custom_fields['genre'][0]); ?></a></li>
				<?php } ?>
			</ul>
		</div>
		 <?php 
		 $book_counter++;
		 endwhile;
		else : ?>		
		<?php endif; wp_reset_postdata();
		} // end of books 
		
		if($view == 'posts'){
		
			$args = array(
		'numberposts' => -1,
		'post_type' => 'book',
		'meta_query'  	=> array(
				  array(
					 'key'           => 'book_author',
					 'value'         => '"'.$post->ID.'"',//quotes to make sure category 23 does not match category 123, 230 etc
					 'compare'       => 'LIKE'
				  )
			   ),
		'orderby' => 'date',
		'order' => 'DESC'
		);

			// get results
			$query_books = new WP_Query( $args );	
			if ($query_books->have_posts()) :
			$book_tags = array();
		    while ($query_books->have_posts()) : $query_books->the_post();
				$book_tags[] = $post->post_name;
			endwhile;
			endif;
			
			if(is_array($book_tags))
			$btags = implode(',',$book_tags);
			
			$btags = $btags.','.$author_page_title;
			$args=array(
			'tag' => $btags,
			//'showposts'=>10,
			'caller_get_posts'=>1
			);
			$my_query = new WP_Query($args);
			if( $my_query->have_posts() ) {
			while ($my_query->have_posts()) : $my_query->the_post(); 
			
			
			$book_tag_info = getBooksByPostTags($post->ID);	
			$book_id = $book_tag_info['book_id'];
			$book_tag_count = $book_tag_info['count'];
			
			$book_info = get_post($book_id);
			//print_r($book_info);
			$book_custom_fields = get_post_custom($book_id);
			//print_r($book_custom_fields);
			$post_vote_count = getvotestypecount($post->ID, 'p');
			$book_vote_count = getvotestypecount($post->ID, 'b');
//			echo "Here I am";
			
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
			<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php the_author_posts(); ?> Posts</a></li>
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
			<li><a href="<?php echo get_permalink($post_book_author[0]->ID); ?>">By <?php the_title(); ?></a></li>
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
			
			</ul>
			<?php } ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php
	endwhile;
	} else { ?>
			<p style="margin-bottom:20px"><em>No posts were found.</em></p>
			<!--<p style="font-family:Helvetica,Arial;text-transform:uppercase;"><strong>Check out one of these posts instead:</strong></p>
			<ul style="margin: 16px 0; padding-left: 30px; list-style: disc outside none;"><li><a href="/post/chosen-darkness-chapter-one">Chosen Darkness  Chapter One</a> by Fawn Atondo</li> <li><a href="/post/in-empires-and-embraces">In Empires and Embraces</a> by Sai Marie Johnson</li> <li><a href="/post/forbidden-fire-book-two-of-the-gateway-chronicles">Forbidden Fire book two of the Gateway Chronicles.</a> by Fawn Atondo</li></ul>-->
	<?php }
	wp_reset_query();  // Restore global post data stomped by the_post().
	
		}
		?>	
	</div>
	<div class="span4-3">
	<div class="group">
	<div class="floral bottom">
	<h3><?php the_title();?>'s Profile</h3>
	</div>
	<?php
	$author_custom_fields = get_post_custom($post->ID);
	$book_author = get_field('book_author');
	$author_data = unserialize($author_custom_fields['badges'][0]);
// 			echo "Marker is here: ";
//	$no_of_books = getNoOfBooksByAuthorId($post->ID);
			$authGid = getAuthorGID($post->ID);
			$no_of_books = getNoOfBooksByAuthorGID($authGid);
	
	?>
	<div class="profile">
	<div class="badge-full">
	<div class="badge-author"></div>
	<?php if($author_custom_fields['profile_image'][0]!=''){ ?>
	<img src="<?php echo $author_custom_fields['profile_image'][0]; ?>">
	<?php } else{ ?>
	<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png">
	<?php } ?>
	</div>
	<ul class="info">
	<li><a href="<?php the_permalink();?>"><?php the_title();?></a></li>
	<li><a href="<?php the_permalink();?>">Author's Profile Page</a></li>
	<li><a href="<?php the_permalink();?>?view=books">Books</a></li>
	</ul>
	<div class="post">
	<p><?php echo $author_custom_fields['profile_text'][0];?></p>
	</div>
	</div>
	<div class="span1">
	<div class="slide-vert">
	<?php if($query_books->post_count>3){ ?>
	<a class="slide-next " href="#"></a>
	<?php } ?>
	<a class="slide-prev hidden" href="#"></a>
	<?php 
	$args = array(
		'nopaging' => true,
		'post_type' => 'book',
		'meta_query'  	=> array(
				  array(
					 'key'           => 'book_author',
					 'value'         => '"'.$post->ID.'"',//quotes to make sure category 23 does not match category 123, 230 etc
					 'compare'       => 'LIKE'
				  )
			   ),
		'orderby' => 'date',
		'order' => 'DESC'
		);

	// get results
	$query_books = new WP_Query( $args );
	if ($query_books->have_posts()) :
			$bcounter = 1;
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_field('book_author');
			
// 			echo "Marker is here: ";
			//$cover_pic = $book_custom_fields['cover_photo_url'][0];
			$cover_pic = str_replace("/large/","/medium/",$book_custom_fields['cover_photo_url'][0]);
		?>
	<a href="<?php the_permalink(); ?>" class="book <?php if($bcounter<=3){?>active<?php }else{ ?> hidden <?php } ?>"><img src="<?php echo $cover_pic; ?>"></a>
	<?php 
		$bcounter++;
		endwhile;
		else : ?>		
		<?php endif; wp_reset_postdata();?>	
	</div>
	<div class="see-more">
	<a href="<?php the_permalink(); ?>?view=books"><i>see all books by <?php the_title();?></i></a>
	</div>
	</div>
	</div>
	<?php get_sidebar();?>
	</div>
	
</div>
<?php
	endwhile;
	else :
	endif; 		
	wp_reset_postdata();
?>
<div class="cf"></div>	
<?php get_footer(); ?>