<?php
/* Template name: Post */
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

get_header(); ?>

	<div class="span8">		
	<div class="floral">
	<h3>Latest Posts &amp; Book Reviews</h3>
	</div>
	
	<?php
	$catname = 'post';
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 2;
	$my_query = new WP_Query();
	$my_query->query('category_name=Uncategorized&post_type='.$catname.'&showposts=9'.'&paged='.$paged.'&orderby=date&order=DESC');
		
	if( $my_query->have_posts() ) {
	while ($my_query->have_posts()) : $my_query->the_post(); 
	
	
	$book_tag_info = getBooksByPostTags($post->ID);	
//	$book_id = $book_tag_info['book_id'];
	$book_id = get_the_ID();

	$book_tag_count = $book_tag_info['count'];
	
	$book_info = get_post($book_id);
	//print_r($book_info);
	$book_custom_fields = get_post_custom($book_id);
//	print_r($book_custom_fields);
	$post_vote_count = getvotescount($post->ID);
	$book_vote_count = getvotescount($book_id);
	
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
			// test 2/17
			$author_name = get_the_author();
			$covert =  unserialize($book_custom_fields['images'][0]);
			$cover = $covert[0][url];
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
			<img src="<?php echo $cover; ?>" style="width:100%">
			</a>
			<ul class="info">
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_info->post_title; ?></a></li>
			<li><a href="<?php echo get_permalink($post_book_author[0]->ID); ?>; ?>">By <?php echo $author_name; ?></a></li>
			<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $talking_cnt;?> People Talking About</a></li>
			
			</ul>
			<?php } ?>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php
	endwhile;
	} // Restore global post data stomped by the_post().
	 ?>	
		<div class="clear"></div>
		<h2 class="prev-page"><?php previous_posts_link('&laquo; Previous') ?></h2>
		<h2 class="next-page"><?php next_posts_link('Next &raquo;', $my_query->max_num_pages) ?></h2>
		<div class="clear"></div>
		<?php wp_reset_query(); wp_reset_postdata(); ?>
	</div>
	<div class="span4">
	<?php get_sidebar();?>
	</div>	

<div class="cf"></div>
<?php get_footer(); ?>
