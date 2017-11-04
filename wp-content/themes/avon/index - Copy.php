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
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post">
			<div class="group bubble" style="background: none repeat scroll 0% 0% rgb(246, 127, 167);">
					<h2><strong><?php the_time('M d') ?></strong> - Book Review</h2>
					<div class="vote">
					<h2 class="pull-left">
					<a href="#" data-slug="book-twinsseparated-at-birth" class="vote-link">Love this post! <span data-slug="book-twinsseparated-at-birth" class="vote-count">4</span> | &hearts;</a>
					</h2>
					<h2 class="pull-right">
					<a href="#" data-slug="lynsay-sands-the-key" class="book-vote-link">Love this book! <span data-slug="lynsay-sands-the-key" class="book-vote-count">54</span> | &hearts;</a>
					</h2>
					</div>
				</div>
				<div class="span5">
					<h1><?php //the_title(); ?></h1>
					<div class="social"></div>
					<div class="post-content">
						<?php //the_content(); ?>
						<?php get_template_part( 'content', get_post_format() ); ?>
					</div>
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
					print_r($author_user);
					?> 
					<h5 class="hidden">Contributor:</h5>
					<div class="badge-full">
					<div class="badge-addict">
					<img src="http://cdna.tid.al/e1990e097fb1829c98ffe5e74df489a2cc01b1d0_100.png">
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
					<!--<h5 class="hidden">Book:</h5>
					<a href="/book/lynsay-sands-the-key">
					<img src="http://static.harpercollins.com/harperimages/isbn/medium_large/4/9780062019714.jpg" style="width:100%">
					</a>
					<ul class="info">
					<li><a href="/book/lynsay-sands-the-key">The Key</a></li>
					<li><a href="/author/lynsay-sands">By Lynsay Sands</a></li>
					<li><a href="/book/lynsay-sands-the-key">71 People Talking About</a></li>
					
					</ul>-->
					</div>
				</div>
		</div>
		<?php endwhile; else: ?>
		<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
	<?php endif; ?>
</div>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>