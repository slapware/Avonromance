<?php
/* Template name: Avon Users */
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
<div class="content community">
	<div class="span8">
		<div class="floral">
		<h4>Community</h4>
		<ul>
		<li><a href="/avon-addicts">Avon Addicts</a></li>
		<li><a href="/book-authors">Authors</a></li>
		<li><a class="active" href="/avon-users">Members</a></li>
		</ul>
		</div>
		<?php
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$catname = 'tidal_contributor';
		//$query_books = new WP_Query();
		//$query_books->query('post_type='.$catname.'&showposts=6'.'&paged='.$paged.'&orderby=date&order=DESC');
		
		$args = array(
		'numberposts' => -1,
		'showposts' => 21,
		'paged' => $paged,
		'post_type' => 'tidal_contributor',
		'meta_query'  	=> array(
				  array(
					 'key'           => 'badges',
					 'value'         => '"Member"',//quotes to make sure category 23 does not match category 123, 230 etc
					 'compare'       => 'LIKE'
				  )
			   ),
		'orderby' => 'post_title',
		'order' => 'ASC'
		);

			// get results
			$query_books = new WP_Query( $args );
		
		$book_counter=1;
		if ($query_books->have_posts()) :
		    while ($query_books->have_posts()) : $query_books->the_post(); 
			$book_custom_fields = get_post_custom($post->ID);
			$book_author = get_the_title();
			
			$no_of_books = getNoOfPostsByAuthorId($post->post_name);
			
			//echo $post->ID;
			//echo "ss".$user_post_count = count_user_posts( $post->ID );
		?>
		<div class="profile <?php if($book_counter%3 == 0){echo "last";}?>">
			<a href="<?php the_permalink(); ?>" class="image">
			<?php if($book_custom_fields['profile_image'][0] != '') { ?>
				<img src="<?php echo $book_custom_fields['profile_image'][0]; ?>">
				<?php } else { ?>
				<img src="http://1d49ac1a2f6db2711e85-048f085edb6fe544d929dd3cb360c213.r83.cf2.rackcdn.com/profile-default.png"><?php } ?>
				<div class="badge-member"></div>
			</a>
			<ul class="info">
				<li><a href="<?php the_permalink(); ?>"><?php the_title();?></a></li>
				<!--<li><a href="<?php the_permalink(); ?>"><?php echo $book_custom_fields['blog_name'][0]; ?></a></li>
				<li><a href="<?php the_permalink(); ?>"><?php echo $no_of_books;?> Posts</a></li>	-->			
			</ul>
		</div>
		 <?php 
		 $book_counter++;
		 endwhile;
		 ?>
		 <div class="clear"></div>
		 <div class="clear"></div>
		 
		 <?php
			
		else : ?>
		<div class="no-posts">Sorry, There are currently no users.</div>
		<?php endif; ?>	
		
		<div class="clear"></div>
		<h2 class="prev-page"><?php previous_posts_link('&laquo; Previous') ?></h2>
		<h2 class="next-page"><?php next_posts_link('Next &raquo;', $query_books->max_num_pages) ?></h2>
		<div class="clear"></div>
		<?php wp_reset_query(); wp_reset_postdata(); ?>
	</div>
	<div class="span4">
	<?php get_sidebar();?>
	</div>	
</div>
<div class="cf"></div>
<?php get_footer(); ?>
