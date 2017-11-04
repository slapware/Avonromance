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
<div class="floral mega">
<h2><?php the_title();?></h2>
</div>
<?php the_content();?>
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p><?php endif; ?>

<?php get_footer(); ?>
