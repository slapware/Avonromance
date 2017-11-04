<?php get_header(); ?>
<div class="span8">
<div class="floral">
<h3>Latest Posts &amp; Book Reviews</h3>
</div>
<style>
.user-profile-image {
display:block;
height:90px;
float:right;
overflow:hidden;
position:relative;
width:90px;
}
.user-profile-image img {
float:none;
}
</style>
<script src="<?php echo get_template_directory_uri(); ?>/vote.js"></script>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<?php
	
	$post_vote_count = getvotescount($post->ID);
	
	$book_tag_info = getBooksByPostTags($post->ID);	
	$book_id = $book_tag_info['book_id'];
	$book_tag_count = $book_tag_info['count'];
	
	$book_info = get_post($book_id);
	$book_author = get_field('book_author', $book_id);
	//print_r($book_info);
	$book_custom_fields = get_post_custom($book_id);
	//print_r($book_custom_fields);

	?>
		<div class="post">
			<div class="group bubble" style="background: none repeat scroll 0% 0% rgb(246, 127, 167);">
					<h2><strong><?php the_time('M d') ?></strong> - Book Review</h2>
					<div class="vote">
					<h2 class="pull-left">
					<a href="#" data-slug="<?php echo $post->ID;?>" class="vote-link">Love this post! <span data-slug="<?php echo $post->ID;?>" class="vote-count"><?php echo $post_vote_count;?></span> | &hearts;</a>
					</h2>
					
					<h2 class="pull-right">
					<?php if($book_id !='' && $book_id !='0'){
					$book_vote_count = getvotescount($book_id);
					?>
					<a href="#" data-slug="<?php echo $book_id;?>" class="book-vote-link">Love this book! <span data-slug="<?php echo $book_id;?>" class="book-vote-count"><?php echo $book_vote_count;?></span> | &hearts;</a>
					<?php } ?>
					</h2>
					</div>
				</div>
				<div class="span5">
					<?php
					$url = $_SERVER['REQUEST_URI'];
					$path_args = explode('/',$url);
					
					$permalink = get_permalink();
					if(substr($permalink, -1) == '/') {
						$permalink = substr($permalink, 0, -1);
					}
					?>	
					<h1><a href="<?php echo $permalink; ?>?from=tag:<?php echo $path_args[2];?>"><?php the_title(); ?></a></h1>
					<div class="social"></div>
					
					<?php the_excerpt(); ?>	
									
					<p><a href="<?php echo $permalink; ?>?from=tag:<?php echo $path_args[2];?>"><i>read more</i></a></p>
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
					//print_r($author_user);
					$author_data = unserialize($custom_fields['badges'][0]);
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
					<img src="<?php echo $custom_fields['profile_image'][0]; ?>">
					</div>
					<ul class="info">
					<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php the_author(); ?></a></li>
					<?php foreach($custom_fields['blog_name'] as $authorblog => $authorbloginfo){ ?>
					<li><a href="<?php echo $custom_fields['blog_url'][$authorblog]; ?>" target="_blank"><?php echo $authorbloginfo;?></a></li>
					<?php } ?>
					<li><a href="/tidal_contributor/<?php echo $author_obj->post_name; ?>"><?php the_author_posts(); ?> Posts</a></li>
					<!--
					<li><a href="#">32 Comments</a></li>
					-->
					</ul>
					</div>
					<div>
					<?php if($book_id !='' && $book_id !='0'){ ?>
					<h5 class="hidden">Book:</h5>
					<a href="<?php echo get_permalink($book_id); ?>">
					<img src="<?php echo $book_custom_fields['cover_photo_url'][0]; ?>" style="width:100%">
					</a>
					<ul class="info">
					<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_info->post_title; ?></a></li>
					<li><a href="<?php echo get_permalink($book_author[0]->ID); ?>">By <?php echo $book_author[0]->post_title; ?></a></li>
					<li><a href="<?php echo get_permalink($book_id); ?>"><?php echo $book_tag_count;?> People Talking About</a></li>
					
					</ul>
					<?php } ?>
					</div>
				</div>
				<div class="clear"></div>
		</div>
		<?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>
</div>
<div class="span4">
<?php get_sidebar(); ?>
</div>
<div class="cf"></div>
<?php get_footer(); ?>