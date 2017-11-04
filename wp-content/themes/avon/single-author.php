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
<div class="content floral-top">
	<div class="span8">
		<div class="floral bottom">
		<h4><?php the_title();?>'s</h4>
		<ul>
		<li><a href="?view=posts">Posts</a></li>
		<li><a href="?view=books" class="active">Books</a></li>
		</ul>
		</div>
	</div>
</div>
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php endif; ?>

<?php get_footer(); ?>