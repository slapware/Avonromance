<?php /**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
// TODO Move this to the header

get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php $blog = get_field('blog_name');?>
<div class="content floral-top">
	<div class="span8">
		<div class="floral bottom">
			<h4><?php if($blog!=''){echo $blog."'s";}else{ the_title(); echo "'s";} ?></h4>
			<ul>
			<li><a href="#" class="active">Posts</a></li>
			</ul>
		</div>
		
			<?php
// 			$book_tag_info = get_post_custom($post->ID);
// 			$tidal_slug = $book_tag_info['tidal_contributor'][0];
//			$avon_user_posts = getPostsByAuthorId($post->post_name);
			$avon_user_posts = getPostTidalContributor($post->post_name);
//			var_dump($avon_user_posts);
			$ids = array();
			foreach($avon_user_posts as $avon_user_post)
			{
				$ids[] = $avon_user_post->ID;
			}
			
			$author_page_title = $post->post_name;			
			
			$args = array(
			'numberposts' => -1,
			'post_type' => 'post',
			'post__in'  	=> $ids,
			'orderby' => 'date',
			'order' => 'DESC'
			);
		
			$my_query = new WP_Query($args);
			if( $my_query->have_posts() ) {
			while ($my_query->have_posts()) : $my_query->the_post(); 
			
			
//			$book_tag_info = getBooksByPostTags($post->ID);	
			$book_tag_info = get_post_custom($post->ID);
			$tidal_slug = $book_tag_info['tidal_contributor'][0];

//			$book_id = $book_tag_info['book_id'];
			$book_tag_count = $book_tag_info['count'];
			// new tag count
			$book_title = get_field('BookTitle');
			$taxonomy = "post_tag"; // can be category, post_tag, or custom taxonomy name
			$term_name = $book_title[0];
			$term = get_term_by('name', $term_name, $taxonomy);
			
//			$book_info = get_post($book_id);
			$book_info = get_post($post->ID);
			$book_id = $book_info->ID;
			//print_r($book_info);
			$book_custom_fields = get_post_custom($book_id);
//			print_r($book_title);
			$post_vote_count = getvotescount($post->ID);
			$book_vote_count = getvotescount($book_id);
			
			$post_book_author = get_field('book_author', $book_id);
//			var_dump($book_tag_count);
//			echo "# = " . getPostsByTag($book_title[0]);
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
			
			<div>
			<?php 
			$cover = getStaticCover($post->ID);
			$bookauthor = getnamebyslug($tidal_slug);
// 			$authorgid = get_field('Author_GID');
// 			$book_author = getAuthorNameByID((int)$authorid[0]);
// 			print_r($book_custom_fields);
//			echo $bookauthor;

//			if($post->ID !='' && $post->ID !='0'){ 
			if($cover !='' && $cover !='0'){ 
			$book_ids[] = $book_id;
			?>
			<h5 class="hidden">Book:</h5>
			<a href="<?php echo get_permalink($book_id); ?>">
			<img src="<?php echo $cover; ?>" style="width:100%">
			</a>
			<ul class="info">
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_info->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($post_book_author[0]->ID); ?>; ?>">By <?php echo $bookauthor; ?></a></li>
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $term->count;?> People Talking About</a></li>
			
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
	 // Restore global post data stomped by the_post().
	?>	
	</div>
	<div class="span4-3">
	<div class="group">
	
	<?php
		$author_user = get_the_author(); 
		$author_obj = get_page_by_title( $author_user, OBJECT, 'tidal_contributor' ); 
		$custom_fields = get_post_custom($author_obj->ID);
		$author_data = unserialize($custom_fields['badges'][0]);
		if($author_data[0]['name'] == 'Member')
		$badge = 'badge-member';
		else
		$badge = 'badge-addict';
		
		$no_of_posts = getNoOfPostsByAuthorId($author_obj->post_name);
	?> 
			
	<div class="floral bottom">
	<h3>
	<?php if($custom_fields['blog_name'][0]!=''){echo $custom_fields['blog_name'][0]."'s Profile";}else{ the_author(); echo "'s Profile";} ?>
	</h3>
	</div>
	
	<div class="profile">
			
			<h5 class="hidden">Contributor:</h5>
			<div class="badge-full">
			<div class="<?php echo $badge;?>">
			<?php if($badge=='Member'){ ?>
			<img src="http://cdna.tid.al/e1990e097fb1829c98ffe5e74df489a2cc01b1d0_100.png">
			<?php } else{ ?>
			<img src="http://c581023.r23.cf2.rackcdn.com/e2e88afe9643dfa049a86d13770139dbae870c30_100.png">
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
			<li><a><?php if($author_data[0]['name'] == 'Member'){echo 'Avon '.$author_data[0]['name'];}else{ echo $author_data[0]['name'];}?></a></li>
			</ul>
			<div class="post">
			<p><?php echo $custom_fields['profile_text'][0];?></p>
			</div>
			</div>
			
	<?php
	$author_custom_fields = get_post_custom($post->ID);
	$book_author = get_field('book_author');
	$author_data = unserialize($author_custom_fields['badges'][0]);
	
	?>
	
	<div class="span1">
	<div class="slide-vert">
	<?php if($query_books->post_count>3){ ?>
	<a class="slide-next " href="#"></a>
	<?php } ?>
	<a class="slide-prev hidden" href="#"></a>
	<?php
	$args = array(
		'numberposts' => -1,
		'post_type' => 'book',
		'post__in'  	=> $book_ids,
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